<?php

namespace App\Filament\Resources\Shipments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ShippingRequestItemForm
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
                    ->columnSpan(3),

                TextInput::make('quantity_requested')
                    ->label('Số lượng yêu cầu')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->columnSpan(1),

                Select::make('status')
                    ->label('Trạng thái')
                    ->options(\App\Enums\ShippingRequestItemStatus::getOptions())
                    ->default(\App\Enums\ShippingRequestItemStatus::PENDING)
                    ->required()
                    ->columnSpan(2),
            ])
            ->columns(6);
    }
}
