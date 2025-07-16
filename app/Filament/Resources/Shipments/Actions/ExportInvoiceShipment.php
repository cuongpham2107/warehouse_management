<?php

namespace App\Filament\Resources\Shipments\Actions;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use App\Exports\ShipmentInvoiceExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportInvoiceShipment extends Action
{
    public static function make(?string $name = null): static
    {
        return parent::make($name)
            ->icon('heroicon-o-arrow-down-tray')
            ->label('Xuất hoá đơn')
            ->action(function (Model $record){
                return Excel::download(new ShipmentInvoiceExport($record), 'shipment_invoice.xlsx');
            });
    }
}