<?php

namespace App\Filament\Resources\ShippingRequests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Enums\ShippingRequestPriority;
use App\Enums\ShippingRequestStatus;

class ShippingRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('request_code')
                    ->label('Mã yêu cầu')
                    ->searchable(),
                TextColumn::make('customer_name')
                    ->label('Tên khách hàng')
                    ->searchable(),
                TextColumn::make('customer_contact')
                    ->label('Liên hệ khách hàng')
                    ->searchable(),
                TextColumn::make('requested_date')
                    ->label('Ngày yêu cầu')
                    ->date()
                    ->sortable(),
                TextColumn::make('priority')
                    ->badge()
                    ->alignCenter()
                    ->label('Mức độ ưu tiên')
                    ->formatStateUsing(fn($state) => $state->getLabel())
                    ->color(fn($state) => $state->getColor())
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->alignCenter()
                    ->label('Trạng thái')
                    ->formatStateUsing(fn($state) => $state->getLabel())
                    ->color(fn($state) => $state->getColor())
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_by')
                    ->label('Người tạo')
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
                SelectFilter::make('priority')
                    ->label('Mức độ ưu tiên')
                    ->options(ShippingRequestPriority::getOptions())
                    ->placeholder('Tất cả mức độ'),
                    
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options(ShippingRequestStatus::getOptions())
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
