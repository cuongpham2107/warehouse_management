<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use App\Models\ShippingRequest;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Maatwebsite\Excel\Facades\Excel;

class ExportInvoiceShipping implements FromView, WithEvents, WithDrawings
{
   protected $shippingRequest;
    protected $items;
    protected $totalPcs = 0;
    protected $totalGrossWeight = 0;

    public function __construct(ShippingRequest $shippingRequest, $items = null)
    {
        $this->shippingRequest = $shippingRequest->load([
            'creator',
            'items.crate',
            'items.crate.pallet'
        ]);

        $this->items = $items ?? $this->shippingRequest->items; 
        $this->calculateTotals();
    }

    public function view(): View
    {
        return view('exports.invoices', [
            'shippingRequest' => $this->shippingRequest,
            'items' => $this->items,
            'totalPcs' => $this->totalPcs,
            'totalGrossWeight' => $this->totalGrossWeight,
        ]);
    }

    protected function calculateTotals()
    {
        foreach ($this->items as $item) {
            if ($item->crate) {
                $this->totalPcs += $item->crate->pcs ?? 0;
                $this->totalGrossWeight += $item->crate->gross_weight ?? 0;
            }
        }
    }
    public function drawings()
    {
        $imagePath = public_path('images/logo.png');
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('ASGL Logo');
        $drawing->setDescription('ASGL Logo');
        $drawing->setPath($imagePath);
        $drawing->setHeight(50);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(5);
        $drawing->setOffsetY(5);

        return [$drawing];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('C5:E14')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C5:E14')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('H5:J14')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H5:J14')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                // // Tùy chỉnh chiều rộng cột
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(10);
                $sheet->getColumnDimension('D')->setWidth(12);
                $sheet->getColumnDimension('F')->setWidth(12);
                $sheet->getColumnDimension('G')->setWidth(12);

            },
        ];
    }

    
}
