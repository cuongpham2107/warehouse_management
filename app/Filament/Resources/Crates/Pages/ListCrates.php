<?php

namespace App\Filament\Resources\Crates\Pages;

use App\Filament\Resources\Crates\CrateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCrates extends ListRecords
{
    protected static string $resource = CrateResource::class;

    public function getTitle(): string
    {
        return 'Danh sách thùng hàng';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tạo mới'),
        ];
    }
}
