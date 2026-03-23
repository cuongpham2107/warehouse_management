<?php

namespace App\Filament\Resources\WarehouseLocations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Enums\WarehouseLocationStatus;

class WarehouseLocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('location_code')
                    ->label('Mã vị trí')
                    ->required()
                    ->placeholder('Nhập mã vị trí'),
            ]);
    }
}
