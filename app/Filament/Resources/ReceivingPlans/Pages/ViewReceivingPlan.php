<?php

namespace App\Filament\Resources\ReceivingPlans\Pages;

use App\Filament\Resources\ReceivingPlans\ReceivingPlanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewReceivingPlan extends ViewRecord
{
    protected static string $resource = ReceivingPlanResource::class;

    public function getTitle(): string
    {
        return 'Xem kế hoạch nhập kho';
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Chỉnh sửa')
                ->icon('heroicon-o-pencil')
                ->outlined(),
        ];
    }
}
