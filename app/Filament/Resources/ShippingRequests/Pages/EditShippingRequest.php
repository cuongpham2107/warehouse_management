<?php

namespace App\Filament\Resources\ShippingRequests\Pages;

use App\Filament\Resources\ShippingRequests\ShippingRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Livewire\Attributes\On;
use App\Filament\Actions\ExportWarehouseAction;

class EditShippingRequest extends EditRecord
{
    protected static string $resource = ShippingRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('nextStep')
                    ->label('Bước tiếp theo')
                    ->icon('heroicon-o-arrow-right')
                    ->color('success')
                    ->visible(fn() => $this->record->canMoveToNextStep())
                    ->requiresConfirmation()
                    ->modalHeading('Chuyển sang bước tiếp theo')
                    ->modalDescription('Bạn có chắc chắn muốn chuyển yêu cầu vận chuyển này sang bước tiếp theo?')
                    ->modalSubmitActionLabel('Xác nhận')
                    ->action(function () {
                        if ($this->record->nextStep()) {
                            $this->refreshFormData(['status']);
                            \Filament\Notifications\Notification::make()
                                ->title('Yêu cầu vận chuyển đã được chuyển sang bước tiếp theo!')
                                ->success()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Không thể chuyển yêu cầu vận chuyển!')
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('cancel_request')
                    ->label('Hủy yêu cầu')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn() => $this->record->canBeCancelled())
                    ->requiresConfirmation()
                    ->modalHeading('Hủy yêu cầu vận chuyển')
                    ->modalDescription('Bạn có chắc chắn muốn hủy yêu cầu vận chuyển này? Hành động này không thể hoàn tác.')
                    ->modalSubmitActionLabel('Hủy yêu cầu')
                    ->action(function () {
                        if ($this->record->cancel()) {
                            $this->refreshFormData(['status']);
                            \Filament\Notifications\Notification::make()
                                ->title('Yêu cầu vận chuyển đã được hủy!')
                                ->success()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Không thể hủy yêu cầu vận chuyển!')
                                ->danger()
                                ->send();
                        }
                    }),

            ])->label('Chuyển đổi trạng thái')
                ->icon('heroicon-m-arrows-right-left')
                ->color('primary')
                ->button(),

            ExportWarehouseAction::make('export_warehouse')
                ->visible(fn() => $this->record->canExport()),
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
