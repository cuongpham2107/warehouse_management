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
                        'check_in' => 'Nhập kho',
                        'check_out' => 'Xuất kho',
                        'move' => 'Di chuyển',
                        'adjust' => 'Điều chỉnh',
                        default => ucfirst($state),
                    })
                    ->color(fn($state) => match($state) {
                        'check_in' => 'success',
                        'check_out' => 'danger',
                        'move' => 'info',
                        'adjust' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),
                    
                TextColumn::make('fromLocation.location_code')
                    ->label('Từ vị trí')
                    ->searchable()
                    ->sortable()
                    ->placeholder('N/A'),
                    
                TextColumn::make('toLocation.location_code')
                    ->label('Đến vị trí')
                    ->searchable()
                    ->sortable()
                    ->placeholder('N/A'),
                    
                TextColumn::make('movement_date')
                    ->label('Ngày di chuyển')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                    
                TextColumn::make('reference_type')
                    ->badge()
                    ->label('Loại tham chiếu')
                    ->formatStateUsing(fn($state) => match($state) {
                        'receiving_plan' => 'Kế hoạch nhập kho',
                        'shipping_request' => 'Yêu cầu vận chuyển',
                        'manual' => 'Thủ công',
                        default => ucfirst($state),
                    })
                    ->color(fn($state) => match($state) {
                        'receiving_plan' => 'info',
                        'shipping_request' => 'warning',
                        'manual' => 'gray',
                        default => 'gray',
                    })
                    ->searchable(),
                    
                TextColumn::make('reference_id')
                    ->label('ID tham chiếu')
                    ->searchable()
                    ->sortable()
                    ->placeholder('N/A'),
                    
                TextColumn::make('performer.name')
                    ->label('Thực hiện bởi')
                    ->searchable()
                    ->sortable()
                    ->placeholder('N/A'),
                    
                TextColumn::make('device_type')
                    ->badge()
                    ->label('Loại thiết bị')
                    ->formatStateUsing(fn($state) => match($state) {
                        'web' => 'Web',
                        'pda' => 'PDA',
                        'forklift_computer' => 'Máy tính xe nâng',
                        default => ucfirst($state),
                    })
                    ->color(fn($state) => match($state) {
                        'web' => 'primary',
                        'pda' => 'success',
                        'forklift_computer' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),
                    
                TextColumn::make('device.device_name')
                    ->label('Thiết bị')
                    ->searchable()
                    ->placeholder('N/A')
                    ->toggleable(),
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
                        'check_in' => 'Nhập kho',
                        'check_out' => 'Xuất kho',
                        'move' => 'Di chuyển',
                        'adjust' => 'Điều chỉnh',
                    ])
                    ->placeholder('Tất cả loại'),
                    
                SelectFilter::make('reference_type')
                    ->label('Loại tham chiếu')
                    ->options([
                        'receiving_plan' => 'Kế hoạch nhập kho',
                        'shipping_request' => 'Yêu cầu vận chuyển',
                        'manual' => 'Thủ công',
                    ])
                    ->placeholder('Tất cả loại tham chiếu'),
                    
                SelectFilter::make('device_type')
                    ->label('Loại thiết bị')
                    ->options([
                        'web' => 'Web',
                        'pda' => 'PDA',
                        'forklift_computer' => 'Máy tính xe nâng',
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
