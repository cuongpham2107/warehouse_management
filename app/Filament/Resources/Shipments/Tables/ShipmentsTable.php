<?php

namespace App\Filament\Resources\Shipments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Enums\ShipmentStatus;

class ShipmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('shipment_code')
                    ->label('Mã lô hàng')
                    ->searchable(),
                TextColumn::make('vehicle.vehicle_code')
                    ->label('Xe tải')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('shippingRequest.request_code')
                    ->label('Yêu cầu vận chuyển')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('departure_time')
                    ->label('Thời gian khởi hành')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('arrival_time')
                    ->label('Thời gian đến')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('total_crates')
                    ->label('Tổng số thùng')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_pieces')
                    ->label('Tổng số sản phẩm')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_weight')
                    ->label('Tổng khối lượng')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->getLabel())
                    ->color(fn($state) => $state->getColor())
                    ->icon(fn($state) => $state->getIcon())
                    ->label('Trạng thái')
                    ->searchable(),
                IconColumn::make('pod_generated')
                    ->label('POD được tạo')
                    ->boolean(),
                TextColumn::make('pod_file_path')
                    ->label('Đường dẫn file POD')
                    ->searchable(),
                TextColumn::make('createdBy.name')
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
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options(ShipmentStatus::getOptions()),
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
            ])
            ->reorderableColumns();
    }
}
