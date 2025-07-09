<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use App\Enums\VehicleType;
use App\Enums\VehicleStatus;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class VehicleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('vehicle_code')
                    ->label('Mã xe'),
                TextEntry::make('vehicle_type')
                    ->label('Loại xe')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => $state instanceof VehicleType ? $state->label() : VehicleType::tryFrom($state)?->label() ?? $state),
                TextEntry::make('license_plate')
                    ->label('Biển số xe')
                    ->copyable(),
                TextEntry::make('driver_name')
                    ->label('Tên tài xế')
                    ->placeholder('Chưa có tài xế'),
                TextEntry::make('driver_phone')
                    ->label('SĐT tài xế')
                    ->placeholder('Chưa có SĐT')
                    ->copyable(),
                TextEntry::make('capacity_weight')
                    ->label('Tải trọng')
                    ->numeric()
                    ->suffix(' kg'),
                TextEntry::make('capacity_volume')
                    ->label('Thể tích')
                    ->numeric()
                    ->suffix(' m³'),
                TextEntry::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn ($state) => $state instanceof VehicleStatus ? $state->color() : VehicleStatus::tryFrom($state)?->color() ?? 'gray')
                    ->icon(fn ($state) => $state instanceof VehicleStatus ? $state->icon() : VehicleStatus::tryFrom($state)?->icon())
                    ->formatStateUsing(fn ($state) => $state instanceof VehicleStatus ? $state->label() : VehicleStatus::tryFrom($state)?->label() ?? $state),
                TextEntry::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime(),
            ]);
    }
}
