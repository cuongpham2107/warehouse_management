<?php

namespace App\Filament\Resources\Shipments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ShipmentItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('crate_id')
                ->label('Chọn kiện hàng')
                ->relationship('crate', 'crate_id')
                ->required()
                ->searchable()
                ->preload()
                ->getOptionLabelFromRecordUsing(fn($record) => "Mã: {$record->crate_id} – Số lượng: {$record->pieces}")
                ->columnSpan(2),
            Select::make('pallet_id')
                ->label('Chọn pallet')
                ->relationship('pallet', 'pallet_id')
                ->required()
                ->searchable()
                ->preload()
                ->columnSpan(2),


            TextInput::make('quantity')
                ->label('Số lượng')
                ->required()
                ->numeric()
                ->default(1)
                ->columnSpan(1),

        ])
            ->columns(4);
    }
}
