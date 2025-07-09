<?php

namespace App\Filament\Resources\WarehouseLocations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WarehouseLocationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('location_code')
                    ->label('Mã vị trí')
                    ->searchable(),
                TextColumn::make('zone')
                    ->label('Khu vực')
                    ->searchable(),
                TextColumn::make('rack')
                    ->label('Giá kệ')
                    ->searchable(),
                TextColumn::make('level')
                    ->label('Tầng')
                    ->alignCenter()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('position')
                    ->label('Vị trí')
                    ->alignCenter()
                    ->searchable(),
                TextColumn::make('max_weight')
                    ->label('Trọng lượng tối đa')
                    ->alignCenter()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_volume')
                    ->label('Thể tích tối đa')
                    ->alignCenter()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(fn($record) => $record->status->getBadgeClass())
                    ->color(fn($record) => $record->status->getColor())
                    ->formatStateUsing(fn($state) => $state->getLabel())
                    ->label('Trạng thái')
                    ->searchable(),
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
                //
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
