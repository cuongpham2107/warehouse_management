<?php

namespace App\Filament\Resources\ReceivingPlans\Pages;

use App\Filament\Resources\ReceivingPlans\ReceivingPlanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Livewire\Attributes\On;


class EditReceivingPlan extends EditRecord
{
    protected static string $resource = ReceivingPlanResource::class;

    public function getTitle(): string
    {
        return 'Chỉnh sửa kế hoạch nhập kho';
    }

    protected function getHeaderActions(): array
    {
        return [
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

    #[On('receivingPlan.refresh')]
    public function refreshReceivingPlan(): void
    {
        $this->refreshFormData([
            'status',
            'total_crates',
            'total_pieces',
            'total_weight',
        ]);
    }
}
