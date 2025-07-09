<?php

namespace App\Filament\Resources\Devices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Enums\DeviceStatus;

class DevicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('device_code')
                    ->label('Mã thiết bị')
                    ->searchable(),
                TextColumn::make('device_type')
                    ->label('Loại thiết bị')
                    ->searchable(),
                TextColumn::make('device_name')
                    ->label('Tên thiết bị')
                    ->searchable(),
                TextColumn::make('mac_address')
                    ->label('Địa chỉ MAC')
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label('Địa chỉ IP')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->getLabel())
                    ->color(fn($state) => $state->getColor())
                    ->label('Trạng thái')
                    ->searchable(),
                TextColumn::make('last_sync_at')
                    ->label('Lần đồng bộ cuối')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('assigned_to')
                    ->label('Được gán cho')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options(DeviceStatus::getOptions())
                    ->placeholder('Tất cả trạng thái'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Xem'),
                EditAction::make()
                    ->label('Chỉnh sửa'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Xóa'),
                ]),
            ]);
    }
}
