<?php

namespace App\Filament\Resources\Vehicles\Tables;

use App\Enums\VehicleType;
use App\Enums\VehicleStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VehiclesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vehicle_code')
                    ->label('Mã xe')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                    
                TextColumn::make('vehicle_type')
                    ->label('Loại xe')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof VehicleType ? $state->label() : (VehicleType::tryFrom($state)?->label() ?? $state))
                    ->color('info'),
                    
                TextColumn::make('license_plate')
                    ->label('Biển số xe')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                    
                TextColumn::make('driver_name')
                    ->label('Tên tài xế')
                    ->searchable()
                    ->toggleable(),
                    
                TextColumn::make('driver_phone')
                    ->label('SĐT tài xế')
                    ->searchable()
                    ->toggleable(),
                    
                TextColumn::make('capacity_weight')
                    ->label('Tải trọng (kg)')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state, 0) . ' kg'),
                    
                TextColumn::make('capacity_volume')
                    ->label('Thể tích (m³)')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . ' m³'),
                    
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn ($state) => $state instanceof VehicleStatus ? $state->color() : (VehicleStatus::tryFrom($state)?->color() ?? 'gray'))
                    ->formatStateUsing(fn ($state) => $state instanceof VehicleStatus ? $state->label() : (VehicleStatus::tryFrom($state)?->label() ?? $state))
                    ->icon(fn ($state) => $state instanceof VehicleStatus ? $state->icon() : (VehicleStatus::tryFrom($state)?->icon() ?? null))
                    ->sortable(),
                    
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options(VehicleStatus::options())
                    ->native(false),
                    
                SelectFilter::make('vehicle_type')
                    ->label('Loại xe')
                    ->options(VehicleType::options())
                    ->native(false),
            ])
            ->recordActions([
                ViewAction::make()->label('Xem'),
                EditAction::make()->label('Sửa'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Xóa'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
