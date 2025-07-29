<?php

namespace App\Filament\Resources\ShippingRequests\Pages;

use App\Filament\Resources\ShippingRequests\ShippingRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Livewire\Attributes\On;
use App\Http\Controllers\ShippingInvoiceExportController;
use Illuminate\Database\Eloquent\Model;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;

class EditShippingRequest extends EditRecord
{
    protected static string $resource = ShippingRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('Xem hoá đơn')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->label('Xem hoá đơn')
                ->url(fn(Model $record) => route('shipping-request.preview-invoice', $record->id))
                ->openUrlInNewTab(),
            \Filament\Actions\Action::make('Xuất hoá đơn')
                ->icon('heroicon-o-arrow-down-tray')
                ->label('Xuất hoá đơn')
                ->action(function (Model $record){
                    if($record->items()->count() == 0) {
                        Notification::make()
                            ->title('Không có items để xuất hoá đơn')
                            ->danger()
                            ->send();
                        return;
                    }
                    return (new ShippingInvoiceExportController())->export($record->id);
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


    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Check if all pallets associated with items have 'shipped' status
        $allPalletsShipped = $record->items()
            ->with('pallet')
            ->get()
            ->every(function ($item): bool {
                return $item->pallet && $item->pallet->status === \App\Enums\PalletStatus::SHIPPED;
            });
        if(!$allPalletsShipped && $record->status === \App\Enums\ShippingRequestStatus::PENDING)
        {
            $data['status'] = \App\Enums\ShippingRequestStatus::IN_PROGRESS->value;
        }
        if ($allPalletsShipped) {
            $data['status'] = \App\Enums\ShippingRequestStatus::COMPLETED->value;
        }
       
        $record->update($data);
        return $record;
    }
}
