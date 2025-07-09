<?php

namespace App\Filament\Resources\Pallets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PalletsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pallet_id')
                    ->label('Mã pallet')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                    
                TextColumn::make('crate.crate_id')
                    ->label('Thùng hàng')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('location.location_code')
                    ->label('Vị trí')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in_transit' => 'warning',
                        'received' => 'info', 
                        'stored' => 'success',
                        'shipped' => 'danger',
                        'damaged' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'in_transit' => 'Đang vận chuyển',
                        'received' => 'Đã nhận',
                        'stored' => 'Đã lưu kho',
                        'shipped' => 'Đã xuất kho',
                        'damaged' => 'Bị hư hỏng',
                        default => ucfirst($state),
                    })
                    
                    ->sortable(),
                    
                TextColumn::make('checked_in_at')
                    ->label('Thời gian nhập kho')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('checkedInBy.name')
                    ->label('Người nhập kho')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('checked_out_at')
                    ->label('Thời gian xuất kho')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('checkedOutBy.name')
                    ->label('Người xuất kho')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
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
                    ->options([
                        'in_transit' => 'Đang vận chuyển',
                        'received' => 'Đã nhận',
                        'stored' => 'Đã lưu kho',
                        'shipped' => 'Đã xuất kho',
                        'damaged' => 'Bị hư hỏng',
                    ])
                    ->native(false),
                    
                SelectFilter::make('location_id')
                    ->label('Vị trí')
                    ->relationship('location', 'location_code')
                    ->searchable()
                    ->preload()
                    ->native(false),
            ])
            ->recordActions([
                ViewAction::make()->label('Xem'),
                EditAction::make()->label('Sửa'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Xóa đã chọn'),
                ])->label('Hành động hàng loạt'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
