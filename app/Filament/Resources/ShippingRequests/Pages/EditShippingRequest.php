<?php

namespace App\Filament\Resources\ShippingRequests\Pages;

use App\Filament\Resources\ShippingRequests\ShippingRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Livewire\Attributes\On;
use App\Exports\ShippingRequestInvoiceExport;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Facades\Filament;

class EditShippingRequest extends EditRecord
{
    protected static string $resource = ShippingRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('Xuất hoá đơn')
                ->icon('heroicon-o-arrow-down-tray')
                ->label('Xuất hoá đơn')
                ->action(function (Model $record){
                    return Excel::download(new ShippingRequestInvoiceExport($record), 'shipping_request_invoice.xlsx');
                }),
            $this->getSaveFormAction()
                ->formId('form')
                ->icon('heroicon-o-check')
                ->label('Lưu'),
            $this->getCancelFormAction()
                ->formId('form')
                ->icon('heroicon-o-x-mark')
                ->label('Hủy'),
            ViewAction::make()
                ->icon('heroicon-o-eye')
                ->label('Xem'),
            DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->label('Xóa'),
        ];
    }
    protected function getFormActions(): array
    {
        return [];
    }

    
     #[On('shippingRequest.refresh')]
    public function refreshShippingRequest(): void
    {
        $this->refreshFormData([
            'status',
        ]);
    }
}
