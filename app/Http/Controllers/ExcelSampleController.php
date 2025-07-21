<?php

namespace App\Http\Controllers;


use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExcelSampleController extends Controller
{
    public function downloadCratesSample()
    {
        $export = new class implements FromArray, WithHeadings, ShouldAutoSize
        {
            public function array(): array
            {
                return [
                    [
                        'no' => 1,
                        'description' => 'Kiện hàng điện tử từ nhà cung cấp ABC',
                        'crate_id' => 'CRATE-ABC001',
                        'pieces' => 25,
                        'type' => 'standard',
                        'gross_weight' => 12.50,
                        'dimensions_length' => 50.00,
                        'dimensions_width' => 30.00,
                        'dimensions_height' => 20.00,
                    ],
                    [
                        'no' => 2,
                        'description' => 'Kiện hàng linh kiện máy tính',
                        'crate_id' => 'CRATE-ABC002',
                        'pieces' => 15,
                        'type' => 'box',
                        'gross_weight' => 8.75,
                        'dimensions_length' => 40.00,
                        'dimensions_width' => 25.00,
                        'dimensions_height' => 15.00,
                    ],
                    [
                        'no' => 3,
                        'description' => 'Kiện hàng thiết bị mạng',
                        'crate_id' => 'CRATE-ABC003',
                        'pieces' => 10,
                        'type' => 'crate',
                        'gross_weight' => 15.30,
                        'dimensions_length' => 60.00,
                        'dimensions_width' => 35.00,
                        'dimensions_height' => 25.00,
                    ],
                    [
                        'no' => 4,
                        'description' => 'Kiện hàng cáp điện thoại',
                        'crate_id' => 'CRATE-ABC004',
                        'pieces' => 50,
                        'type' => 'pallet',
                        'gross_weight' => 22.80,
                        'dimensions_length' => 45.00,
                        'dimensions_width' => 28.00,
                        'dimensions_height' => 18.00,
                    ],
                    [
                        'no' => 5,
                        'description' => 'Kiện hàng thiết bị y tế',
                        'crate_id' => 'CRATE-ABC005',
                        'pieces' => 5,
                        'type' => 'box',
                        'gross_weight' => 6.20,
                        'dimensions_length' => 30.00,
                        'dimensions_width' => 20.00,
                        'dimensions_height' => 12.00,
                    ],
                
                ];
            }

            public function headings(): array
            {
                return [
                    'no' => "NO.",
                    'description' => "Description of Goods",
                    'crate_id' => "Crate ID",
                    'pieces' => "Quantity",
                    'type' => "Packing Type",
                    'gross_weight' => "Gross Weight (kg)",
                    'dimensions_length' => "Dimensions Length (cm)",
                    'dimensions_width' => "Dimensions Width (cm)",
                    'dimensions_height' => "Dimensions Height (cm)",
                ];
            }
        };

        return Excel::download($export, 'crates_import_sample.xlsx');
    }
}
