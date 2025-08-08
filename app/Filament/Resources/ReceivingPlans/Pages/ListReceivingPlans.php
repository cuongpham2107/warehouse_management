<?php

namespace App\Filament\Resources\ReceivingPlans\Pages;

use App\Filament\Resources\ReceivingPlans\ReceivingPlanResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Collection;

class ListReceivingPlans extends ListRecords
{
    protected static string $resource = ReceivingPlanResource::class;

    public function getTitle(): string
    {
        return 'Danh sách kế hoạch nhập kho';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tạo kế hoạch nhập kho mới')
                ->icon('heroicon-o-plus')
                ->modal('create_receiving_plan')
                ->modalWidth(Width::SevenExtraLarge),
        ];
    }
}
