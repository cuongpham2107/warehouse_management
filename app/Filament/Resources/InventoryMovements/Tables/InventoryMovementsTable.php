<?php

namespace App\Filament\Resources\InventoryMovements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InventoryMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pallet.pallet_id')
                    ->label('Pallet')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('movement_type')
                    ->badge()
                    ->label('Loại di chuyển')
                    ->formatStateUsing(fn($state) => match($state) {
                        'transfer' => 'Di chuyển',
                        'relocate' => 'Điều chỉnh',
                        default => ucfirst($state),
                    })
                    ->color(fn($state) => match($state) {
                        'transfer' => 'primary',
                        'relocate' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),
                    
                TextColumn::make('from_location_code')
                    ->label('Từ vị trí')
                    ->searchable()
                    ->sortable()
                    ->placeholder('N/A'),
                    
                TextColumn::make('to_location_code')
                    ->label('Đến vị trí')
                    ->searchable()
                    ->sortable()
                    ->placeholder('N/A'),
                    
                TextColumn::make('movement_date')
                    ->label('Ngày di chuyển')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                    
                
                TextColumn::make('performer.name')
                    ->label('Thực hiện bởi')
                    ->searchable()
                    ->sortable()
                    ->placeholder('N/A'),
                    
                TextColumn::make('device_type')
                    ->badge()
                    ->label('Loại thiết bị')
                    ->formatStateUsing(fn($state) => match($state) {
                        'scanner' => 'Máy quét',
                        'manual' => 'Thủ công',
                        default => ucfirst($state),
                    })
                    ->color(fn($state) => match($state) {
                        'scanner' => 'primary',
                        'manual' => 'success',
                        default => 'gray',
                    })
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
                SelectFilter::make('movement_type')
                    ->label('Loại di chuyển')
                    ->options([
                        'transfer' => 'Chuyển kho',
                        'relocate' => 'Di chuyển vị trí',
                    ])
                    ->placeholder('Tất cả loại'),
                    
               
                    
                SelectFilter::make('device_type')
                    ->label('Loại thiết bị')
                    ->options([
                        'scanner' => 'Máy quét',
                        'manual' => 'Thủ công',
                    ])
                    ->placeholder('Tất cả thiết bị'),
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
