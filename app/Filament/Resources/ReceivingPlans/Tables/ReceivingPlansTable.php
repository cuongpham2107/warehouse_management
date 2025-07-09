<?php

namespace App\Filament\Resources\ReceivingPlans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReceivingPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plan_code')
                    ->label('Mã kế hoạch')
                    ->searchable(),
                TextColumn::make('vendor.vendor_name')
                    ->label('Nhà cung cấp')
                    ->sortable(),
                TextColumn::make('plan_date')
                    ->label('Ngày kế hoạch')
                    ->date()
                    ->sortable(),
                TextColumn::make('total_crates')
                    ->alignCenter(true)
                    ->label('Tổng số thùng')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_pieces')
                    ->alignCenter(true)
                    ->label('Tổng số sản phẩm')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_weight')
                    ->alignCenter(true)
                    ->label('Tổng khối lượng')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->getLabel())
                    ->color(fn($state) => $state->getColor())
                    ->label('Trạng thái')
                    ->searchable(),
                TextColumn::make('creator.name')
                    ->label('Người tạo')
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
