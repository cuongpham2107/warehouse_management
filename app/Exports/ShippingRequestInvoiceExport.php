<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Events\AfterSheet;
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
        
        // Logo và header (dòng 1-3)
        // Chỉ cần một dòng, nội dung sẽ là RichText ở D1
        $rows[] = ["", "", "", '', "", "", "", "", "", ""];
        $rows[] = ["", "", "", '', "", "", "", "", "", ""];
        $rows[] = array_fill(0, 10, ''); // Dòng trống
        
        // Thông tin chi tiết (dòng 4-12)
        $rows[] = array_fill(0, 10, ''); // Dòng trống
        $rows[] = [
            '', '', '', '', '', '',  '', '','', ''
        ];
        
        $rows[] = [
            '', '', '', '', '', '', '','', '', ''
        ];
        $rows[] = [
            '', '', '', '', '', '', '', '', '', ''
        ];
        $rows[] = [
            '', '', '', '', '', '', '', '', '', ''
        ];
        $rows[] = [
            'TG xuất phát tại ASGL (Time of depart at ASGL):', '', '', '', '', 'TG bắt đầu dỡ hàng (Time of begin unload):', '', '', '', ''
        ];
        $rows[] = [
            'TG xe đến (Time of Arrival):', '', '', '', '', 'TG kết thúc dỡ hàng (Time of Finish unload):', '', '', '', ''
        ];
        $rows[] = [
            'Tổng số Pallet số:', '', '', '', '', 'Địa điểm giao hàng (Warehouser):', '', '', '', ''
        ];
        $rows[] = array_fill(0, 10, ''); // Dòng trống

        // Header bảng (dòng 13-15)
        $rows[] = array_fill(0, 10, ''); // Dòng trống
        $rows[] = [
            "STT", "Crate_ID", "Số kiện\n(PCS)", "Trọng lượng\n(Gr.Weight)", "Pallet", "Carton\n(CTN)", "Số lượng\n(Q.ty)", "Ghi chú\nRemark", '', ''
        ];
        $rows[] = array_fill(0, 10, ''); // Dòng trống cho merge

        // Dữ liệu bảng
        dd($this->shippingRequest->items);
        foreach ($this->shippingRequest->items as $i => $item) {
            $crate = $item->crate;
            $pallet = $crate ? $crate->pallet : null;
            $rows[] = [
                $i + 1,
                $crate ? $crate->crate_id : '',
                $crate->pieces ?? '',
                $crate->gross_weight ?? '',
                $pallet ? $pallet->pallet_id : '',
                '', // Carton
                '', // Số lượng
                $item->notes ?? '',
                '', ''
            ];
        }

        // Thêm các dòng trống để có đủ 20 dòng dữ liệu như mẫu
        $currentItemCount = count($this->shippingRequest->items);
        for ($i = $currentItemCount; $i < 20; $i++) {
            $rows[] = [
                $i + 1, '', '', '', '', '', '', '', '', ''
            ];
        }

        // Dòng TOTAL
        $rows[] = [
            '', 'TOTAL', '', '', '', '', '', '', '', ''
        ];

        // Phần ký tên
        $rows[] = array_fill(0, 10, ''); // Dòng trống
        $rows[] = [
            '', '', '', '', '', '', '', '', '', ''
        ];
        $rows[] = [
            'CÔNG TY CỔ PHẦN LOGISTICS ASGL', '', '', '', '', '', '', '', '', ''
        ];
        $rows[] = [
            'Lô số 5, KCN Yên Bình, Phường Vạn Xuân, Tỉnh Thái Nguyên, Việt Nam', '', '', '', '', '', 'Họ tên và chữ ký:', '', '', ''
        ];
        $rows[] = [
            '', '', '', '', '', '', '', '', '', ''
        ];
        $rows[] = [
            '', '', '', '', '', '', '', '', '', ''
        ];
        $rows[] = [
            '', '', '', '', '', '', '', '', ''
        ];
        $rows[] = [
            '', '', '', '', '', '', '', '', '', ''
        ];
        $rows[] = [
            '', '', '', '', '', '', '', '', '', ''
        ];
        $rows[] = [
            '', '', '', '', '', '', '', '', '', ''
        ];
        $rows[] = [
            '(tên khách hàng)', '', '', '', '', '', '', '', '', ''
        ];
        $rows[] = [
            '(địa chỉ giao hàng)', '', '', '', '', '', '', '', '', ''
        ];
        $rows[] = [
            '', '', '', '', '', '', '', '', '', ''
        ];

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
                $sheet = $event->sheet->getDelegate();

                // Merge cells cho logo và header
                $sheet->mergeCells('A1:C3');
                $sheet->mergeCells('D1:J3'); // Merge toàn bộ vùng D1:J3
                $sheet->getStyle('A1:C3')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);
                $sheet->getStyle('D1:J3')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);
                // Thêm logo vào vị trí A1:C3
                $logoPath = public_path('images/logo.png');
                if (file_exists($logoPath)) {
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setName('Logo');
                    $drawing->setDescription('Company Logo');
                    $drawing->setPath($logoPath);
                    $drawing->setHeight(60); // Chiều cao logo
                    $drawing->setCoordinates('A1');
                    $drawing->setOffsetX(10);
                    $drawing->setOffsetY(5);
                    $drawing->setWorksheet($sheet);
                }
                $sheet->mergeCells('A4:J4');
                // Merge cells cho thông tin chi tiết
                $sheet->mergeCells('A5:C6');
                $sheet->mergeCells('D5:E6');
                $sheet->mergeCells('F5:G6');
                $sheet->mergeCells('H5:J6');
                // Không merge H6:J6 để tránh giao vùng

                $sheet->mergeCells('A7:C8');
                $sheet->mergeCells('D7:E8');
                $sheet->mergeCells('F7:G8');
                $sheet->mergeCells('H7:J8');
                $sheet->mergeCells('A9:C9');
                $sheet->mergeCells('D9:E9');
                $sheet->mergeCells('F9:G9');
                $sheet->mergeCells('H9:J9');
                $sheet->mergeCells('A10:C10');
                $sheet->mergeCells('D10:E10');
                $sheet->mergeCells('F10:G10');
                $sheet->mergeCells('H10:J10');
                $sheet->mergeCells('A11:C11');
                $sheet->mergeCells('D11:E11');
                $sheet->mergeCells('F11:G11');
                $sheet->mergeCells('H11:J11');
                $sheet->mergeCells('A12:J13');
                
                $sheet->mergeCells('G38:J41');
                $sheet->mergeCells('G42:J45');
                $sheet->mergeCells('G46:J49');
                $sheet->mergeCells('A38:F38');
                $sheet->mergeCells('A46:F46');

                // Tạo RichText cho D1
                $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $run1 = $richText->createTextRun("BIÊN BẢN BÀN GIAO HÀNG HÓA\n");
                $run1->getFont()->setBold(true)->setSize(14);
                $run2 = $richText->createTextRun("PROOF OF DELIVERY");
                $run2->getFont()->setBold(false)->setSize(13);
                $sheet->setCellValue('D1', $richText);


                // RichText cho A5 (A5:C6)
                $richTextA5 = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $runBold = $richTextA5->createTextRun('Tháng/ngày/năm: ');
                $runBold->getFont()->setBold(true)->setSize(11);
                $runItalic = $richTextA5->createTextRun("\n(dd/mm/yyyy)");
                $runItalic->getFont()->setItalic(true)->setSize(11);
                $sheet->setCellValue('A5', $richTextA5);
                $sheet->getStyle('A5:C6')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);
                //Merge cells D5:E6
                $sheet->setCellValue('D5', date('d/m/Y', strtotime($this->shippingRequest->requested_date)) ?? '');
                $sheet->getStyle('D5:E6')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                // RichText cho A7 (A7:C8)
                $richTextA7 = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $runBoldA7 = $richTextA7->createTextRun('Biến số xe: ');
                $runBoldA7->getFont()->setBold(true)->setSize(11);
                $runItalicA7 = $richTextA7->createTextRun("\n(Truck No.)");
                $runItalicA7->getFont()->setItalic(true)->setSize(11);
                $sheet->setCellValue('A7', $richTextA7);
                $sheet->getStyle('A7:C8')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);
                //Merge cells D7:E8
                $sheet->setCellValue('D7', $this->shippingRequest->license_plate ?? '');
                $sheet->getStyle('D7:E8')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                
                 // RichText cho F5 (F5:G6)
                $richTextF5 = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $runBoldF5 = $richTextF5->createTextRun('Số biên bản: ');
                $runBoldF5->getFont()->setBold(true)->setSize(11);
                $runItalicF5 = $richTextF5->createTextRun("\nPOD No.: ");
                $runItalicF5->getFont()->setItalic(true)->setSize(11);
                $sheet->setCellValue('F5', $richTextF5);
                $sheet->getStyle('F5:G6')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);
                // Merge cells H5:J6
                $sheet->setCellValue('H5', $this->shippingRequest->request_code ?? '');
                $sheet->getStyle('H5:J6')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                // RichText cho F7 (F7:G8)
                $richTextF7 = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $runBoldF7 = $richTextF7->createTextRun('Số niêm phong: ');
                $runBoldF7->getFont()->setBold(true)->setSize(11);
                $runItalicF7 = $richTextF7->createTextRun("\nSeal No.:");
                $runItalicF7->getFont()->setItalic(true)->setSize(11);
                $sheet->setCellValue('F7', $richTextF7);
                $sheet->getStyle('F7:G8')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);
                // Merge cells H7:J8
                $sheet->setCellValue('H7', $this->shippingRequest->seal_number ?? '');
                $sheet->getStyle('H7:J8')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);
                $richTextA9 = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $runBoldA9 = $richTextA9->createTextRun('TG xuất phát tại ASGL: ');
                $runBoldA9->getFont()->setBold(true)->setSize(11);
                $runItalicA9 = $richTextA9->createTextRun("\n(Time of depart at ASGL):");
                $runItalicA9->getFont()->setItalic(true)->setSize(11);
                $sheet->setCellValue('A9', $richTextA9);

                $richTextF9 = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $runBoldF9 = $richTextF9->createTextRun('TG bắt đầu dỡ hàng: ');
                $runBoldF9->getFont()->setBold(true)->setSize(11);
                $runItalicF9 = $richTextF9->createTextRun("\n(Time of begin unload):");
                $runItalicF9->getFont()->setItalic(true)->setSize(11);
                $sheet->setCellValue('F9', $richTextF9);

                $richTextA10 = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $runBoldA10 = $richTextA10->createTextRun('TG xe đến: ');
                $runBoldA10->getFont()->setBold(true)->setSize(11);
                $runItalicA10 = $richTextA10->createTextRun("\n(Time of Arrival):");
                $runItalicA10->getFont()->setItalic(true)->setSize(11);
                $sheet->setCellValue('A10', $richTextA10);

                $richTextF10 = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $runBoldF10 = $richTextF10->createTextRun('TG kết thúc dỡ hàng: ');
                $runBoldF10->getFont()->setBold(true)->setSize(11);
                $runItalicF10 = $richTextF10->createTextRun("\n(Time of Finish unload):");
                $runItalicF10->getFont()->setItalic(true)->setSize(11);
                $sheet->setCellValue('F10', $richTextF10);


                $richTextA11 = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $runBoldA11 = $richTextA11->createTextRun('Tổng số Pallet số: ');
                $runBoldA11->getFont()->setBold(true)->setSize(11);
                $runItalicA11 = $richTextA11->createTextRun("\n(Total number of pallets):");
                $runItalicA11->getFont()->setItalic(true)->setSize(11);
                $sheet->setCellValue('A11', $richTextA11);

                $richTextF11 = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $runBoldF11 = $richTextF11->createTextRun('Địa điểm giao hàng: ');
                $runBoldF11->getFont()->setBold(true)->setSize(11);
                $runItalicF11 = $richTextF11->createTextRun("\n(Warehouse):");
                $runItalicF11->getFont()->setItalic(true)->setSize(11);
                $sheet->setCellValue('F11', $richTextF11);


                
                // Style cho vùng merge
                $sheet->getStyle('D1:J3')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                $ricDeliverer = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $runDeliverer = $ricDeliverer->createTextRun("DELIVERER");
                $runDeliverer->getFont()->setBold(true)->setSize(14);
                $sheet->setCellValue('A38', $ricDeliverer);

                $sheet->getStyle('A38:F38')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);
                

                $richReceiver = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $runReceiver = $richReceiver->createTextRun("RECEIVER");
                $runReceiver->getFont()->setBold(true)->setSize(14);
                $sheet->setCellValue('A46', $richReceiver);

                $sheet->getStyle('A46:F46')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);
                $rickCustomer = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $runCustomer = $rickCustomer->createTextRun("Nhân viên khai thác ASGL\n");
                $runCustomer->getFont()->setBold(true)->setSize(14);
                $runCustomer2 = $rickCustomer->createTextRun("Operation staff:...............");
                $runCustomer2->getFont()->setBold(false)->setItalic(true)->setSize(13);
                $runCustomer3 = $rickCustomer->createTextRun("\nHọ tên và chữ ký:");
                $runCustomer3->getFont()->setBold(true)->setSize(14);
                $runCustomer4 = $rickCustomer->createTextRun("\nName and signature:...............");
                $runCustomer4->getFont()->setBold(false)->setItalic(true)->setSize(13);
                $sheet->setCellValue('G38', $rickCustomer);
                $sheet->getStyle('G38:J41')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);
                $rickCustomerVehicle = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $rickCustomerVehicleRun1 = $rickCustomerVehicle->createTextRun("Nhân viên lái xxe tải\n");
                $rickCustomerVehicleRun1->getFont()->setBold(true)->setSize(14);
                $rickCustomerVehicleRun2 = $rickCustomerVehicle->createTextRun("Driver: " . ($this->shippingRequest->driver_name ?? '') . ".");
                $rickCustomerVehicleRun2->getFont()->setBold(false)->setItalic(true)->setSize(13);
                $rickCustomerVehicleRun3 = $rickCustomerVehicle->createTextRun("\nHọ tên và chữ ký:");
                $rickCustomerVehicleRun3->getFont()->setBold(true)->setSize(14);
                $rickCustomerVehicle4 = $rickCustomerVehicle->createTextRun("\nName and signature:...............");
                $rickCustomerVehicle4->getFont()->setBold(false)->setItalic(true)->setSize(13);
                $sheet->setCellValue('G42', $rickCustomerVehicle);

                $sheet->getStyle('G42:J45')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                $richCustomerReceiver = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $runCustomerReceiver1 = $richCustomerReceiver->createTextRun("Nhân viên nhận hàng\n");
                $runCustomerReceiver1->getFont()->setBold(true)->setSize(14);
                $runCustomerReceiver2 = $richCustomerReceiver->createTextRun("Receiver staff:...............");
                $runCustomerReceiver2->getFont()->setBold(false)->setItalic(true)->setSize(13);
                $runCustomerReceiver3 = $richCustomerReceiver->createTextRun("\nHọ tên và chữ ký:");
                $runCustomerReceiver3->getFont()->setBold(true)->setSize(14);
                $runCustomerReceiver4 = $richCustomerReceiver->createTextRun("\nName and signature:...............");
                $runCustomerReceiver4->getFont()->setBold(false)->setItalic(true)->setSize(13);
                $sheet->setCellValue('G46', $richCustomerReceiver);
                $sheet->getStyle('G46:J49')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);


                // Border cho toàn bộ vùng chính
                $sheet->getStyle('A1:J' . $sheet->getHighestRow())->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                 // Style căn giữa cho D5:E6 và D7:E8
                $sheet->getStyle('D5:E6')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);
                $sheet->getStyle('H5:J6')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);
                $sheet->getStyle('D7:E8')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                // Merge cells cho header bảng (A14:A15, B14:B15, ...)
                $headerRow1 = 14;
                $headerRow2 = 15;
                $sheet->mergeCells("A{$headerRow1}:A{$headerRow2}");
                $sheet->mergeCells("B{$headerRow1}:B{$headerRow2}");
                $sheet->mergeCells("C{$headerRow1}:C{$headerRow2}");
                $sheet->mergeCells("D{$headerRow1}:D{$headerRow2}");
                $sheet->mergeCells("E{$headerRow1}:E{$headerRow2}");
                $sheet->mergeCells("F{$headerRow1}:F{$headerRow2}");
                $sheet->mergeCells("G{$headerRow1}:G{$headerRow2}");
                $sheet->mergeCells("H{$headerRow1}:J{$headerRow2}");
                // Border và style cho bảng dữ liệu
                $dataStartRow = 14; // Dòng bắt đầu dữ liệu
                $dataEndRow = $dataStartRow + 22; // 20 dòng dữ liệu + 2 dòng header + 1 dòng total
                
                // Header bảng (merged cells A14:A15, B14:B15, etc.)
                $sheet->getStyle("A{$dataStartRow}:J{$headerRow2}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 10,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                // Border cho toàn bộ bảng dữ liệu (từ dòng 16 trở đi)
                $dataBodyStartRow = 16;
                $sheet->getStyle("A{$dataBodyStartRow}:J{$dataEndRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);
                foreach (range($dataBodyStartRow, $dataEndRow ) as $row) {
                    $sheet->mergeCells("H{$row}:J{$row}");
                }
                // Style cho dòng TOTAL
                $totalRow = $dataEndRow;
                $sheet->getStyle("A{$totalRow}:J{$totalRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);

                // Đặt width cho các cột
                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(15);
                $sheet->getColumnDimension('C')->setWidth(12);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(5);
                $sheet->getColumnDimension('J')->setWidth(5);

                // Đặt height cho header rows để hiển thị wrap text tốt hơn
                $sheet->getRowDimension(14)->setRowHeight(35);
                $sheet->getRowDimension(15)->setRowHeight(35);

                // Đặt height cho các dòng khác
                for ($i = 1; $i <= $sheet->getHighestRow(); $i++) {
                    if ($i != 14 && $i != 15) {
                        $sheet->getRowDimension($i)->setRowHeight(20);
                    }
                }

                // Font size mặc định
                $sheet->getStyle('A1:J' . $sheet->getHighestRow())->getFont()->setSize(10);
            },
        ];
    }
}