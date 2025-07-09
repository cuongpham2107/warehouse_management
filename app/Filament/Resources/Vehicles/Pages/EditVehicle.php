<?php

namespace App\Filament\Resources\Vehicles\Pages;

use App\Filament\Resources\Vehicles\VehicleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditVehicle extends EditRecord
{
    protected static string $resource = VehicleResource::class;

    public function getTitle(): string
    {
        return 'Chỉnh sửa xe tải';
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Xem'),
            DeleteAction::make()
                ->label('Xóa'),
        ];
    }
}
