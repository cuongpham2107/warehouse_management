<?php

namespace App\Filament\Resources\ShippingRequests\Pages;

use App\Filament\Resources\ShippingRequests\ShippingRequestResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use App\Filament\Actions\ExportWarehouseAction;

class ViewShippingRequest extends ViewRecord
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
                Action::make('cancel')
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
            EditAction::make()
                ->icon('heroicon-o-pencil')
                ->label('Chỉnh sửa')
                ->outlined(),
            ExportWarehouseAction::make('export_warehouse')
                ->visible(fn() => $this->record->canExport()),

        ];
    }
}
