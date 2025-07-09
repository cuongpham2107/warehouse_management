<?php

namespace App\Filament\Resources\Crates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CratesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('crate_id')
                    ->label('Mã thùng hàng')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                    
                TextColumn::make('receivingPlan.plan_code')
                    ->label('Kế hoạch nhập kho')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('pieces')
                    ->label('Số lượng')
                    ->alignCenter(true)
                    ->numeric()
                    ->sortable(),
                    
                TextColumn::make('gross_weight')
                    ->label('Trọng lượng')
                    ->alignCenter(true)
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . ' kg'),
                    
                TextColumn::make('dimensions')
                    ->label('Kích thước (L×W×H)')
                    ->getStateUsing(function ($record) {
                        $l = $record->dimensions_length ?? 0;
                        $w = $record->dimensions_width ?? 0;
                        $h = $record->dimensions_height ?? 0;
                        return "{$l} × {$w} × {$h} cm";
                    })
                    ->toggleable(),
                    
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'planned' => 'gray',
                        'received' => 'info',
                        'checked_in' => 'warning',
                        'stored' => 'success',
                        'shipped' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'planned' => 'Đã lên kế hoạch',
                        'received' => 'Đã nhận',
                        'checked_in' => 'Đã kiểm tra nhập kho',
                        'stored' => 'Đã lưu kho',
                        'shipped' => 'Đã xuất kho',
                        default => ucfirst($state),
                    })
                    ->sortable(),
                    
                TextColumn::make('barcode')
                    ->label('Mã vạch')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),
                    
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
                        'planned' => 'Đã lên kế hoạch',
                        'received' => 'Đã nhận',
                        'checked_in' => 'Đã kiểm tra nhập kho',
                        'stored' => 'Đã lưu kho',
                        'shipped' => 'Đã xuất kho',
                    ])
                    ->native(false),
                    
                SelectFilter::make('receiving_plan_id')
                    ->label('Kế hoạch nhập kho')
                    ->relationship('receivingPlan', 'plan_code')
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
