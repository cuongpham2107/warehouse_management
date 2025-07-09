<?php

namespace App\Filament\Resources\Crates\Pages;

use App\Filament\Resources\Crates\CrateResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCrate extends ViewRecord
{
    protected static string $resource = CrateResource::class;

    public function getTitle(): string
    {
        return 'Xem thùng hàng';
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('Sửa'),
        ];
    }
}
