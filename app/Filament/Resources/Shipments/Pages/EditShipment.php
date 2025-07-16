<?php

namespace App\Filament\Resources\Shipments\Pages;

use App\Filament\Resources\Shipments\ShipmentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use App\Enums\ShipmentStatus;
use Livewire\Attributes\On;
use Livewire\Component;
use App\States\DeliveredState;
use App\Filament\Resources\Shipments\Actions\ExportInvoiceShipment;

class EditShipment extends EditRecord
{
    protected static string $resource = ShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportInvoiceShipment::make('export_invoice_shipment'),
            ActionGroup::make([
                Action::make('switch_status_ready')
                    ->label('Sẵn sàng')
                    ->action(function (Component $livewire) {
                        $this->record->status = ShipmentStatus::READY;
                        $this->record->save();
                        \Filament\Notifications\Notification::make()
                            ->title('Chuyển trạng thái thành công')
                            ->body('Đơn hàng đã chuyển sang trạng thái sẵn sàng')
                            ->success()
                            ->send();
                        $livewire->dispatch('shipment.refresh');
                    })
                    ->color('success')
                    ->icon('heroicon-o-check-badge')
                    ->visible(fn () => $this->record->status === ShipmentStatus::LOADING),
                Action::make('switch_status_departed')
                    ->label('Đã khởi hành')
                    ->action(function (Component $livewire) {
                        $this->record->status = ShipmentStatus::DEPARTED;
                        $this->record->save();
                        \Filament\Notifications\Notification::make()
                            ->title('Chuyển trạng thái thành công')
                            ->body('Đơn hàng đã chuyển sang trạng thái đã khởi hành')
                            ->success()
                            ->send();
                        $livewire->dispatch('shipment.refresh');
                    })
                    ->color('primary')
                    ->icon('heroicon-o-truck')
                    ->visible(fn () => $this->record->status === ShipmentStatus::READY),
                Action::make('switch_status_delivered')
                    ->label('Đã giao hàng')
                    ->action(function (Component $livewire) {
                        $this->record->status = ShipmentStatus::DELIVERED;  
                        $this->record->shippingRequest->status = DeliveredState::class; 
                        $this->record->shippingRequest->save();
                        $this->record->save();
                        \Filament\Notifications\Notification::make()
                            ->title('Chuyển trạng thái thành công')
                            ->body('Đơn hàng đã chuyển sang trạng thái đã giao hàng')
                            ->success()
                            ->send();
                        $livewire->dispatch('shipment.refresh');
                    })
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn () => $this->record->status === ShipmentStatus::DEPARTED),
                Action::make('switch_status_returned')
                    ->label('Đã trả về')
                    ->action(function (Component $livewire) {
                        $this->record->status = ShipmentStatus::RETURNED;
                        $this->record->save();
                        \Filament\Notifications\Notification::make()
                            ->title('Chuyển trạng thái thành công')
                            ->body('Đơn hàng đã chuyển sang trạng thái đã trả về')
                            ->success()
                            ->send();
                        $livewire->dispatch('shipment.refresh');
                    })
                    ->color('danger')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->visible(fn () => $this->record->status === ShipmentStatus::DELIVERED),
            ])
            ->label('Chuyển trạng thái đơn hàng')
            ->icon('heroicon-o-arrow-down-on-square-stack')
            ->button(),
            $this->getSaveFormAction()
                ->formId('form')
                ->icon('heroicon-o-check')
                ->label('Lưu'),
            $this->getCancelFormAction()
                ->formId('form')
                ->icon('heroicon-o-x-mark')
                ->label('Hủy'),
            ViewAction::make(),
            DeleteAction::make(),

          
            
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    #[On('shipment.refresh')]
    public function refreshShipment(): void
    {
        $this->refreshFormData([
            'status',
        ]);
    }
}
