<?php

namespace App\Filament\Resources\ShippingRequests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use App\Enums\ShippingRequestStatus;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Enums\RecordActionsPosition;

class ShippingRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('request_code')
                    ->label('Mã yêu cầu')
                    ->width('12%')
                    ->weight(FontWeight::Bold)
                    ->searchable(),
                TextColumn::make('license_plate')
                    ->label('Biển số xe')
                    ->searchable(),
                TextColumn::make('driver_name')
                    ->label('Tên tài xế')
                    ->searchable(),
                TextColumn::make('driver_phone')
                    ->label('SĐT tài xế')
                    ->searchable(),
                TextColumn::make('seal_number')
                    ->label('Số niêm phong')
                    ->searchable(),
                TextColumn::make('customer_name')
                    ->label('Tên khách hàng')
                    ->width('15%')
                    ->searchable(),
                TextColumn::make('customer_contact')
                    ->label('Liên hệ khách hàng')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('requested_date')
                    ->label('TG yêu cầu')
                    ->badge()
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('departure_time')
                    ->label('TG xuất phát')
                    ->badge()
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->getLabel())
                    ->color(fn($state) => $state->getColor()),
                TextColumn::make('creator.name')
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
                SelectFilter::make('created_by')
                    ->label('Người tạo')
                    ->relationship('creator', 'name'),
                Filter::make('requested_date')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('value')
                            ->label('Ngày yêu cầu (YYYY-MM-DD)')
                            ->placeholder('Nhập ngày yêu cầu'),
                    ])
                    ->query(function ($query, $data) {
                        if (!empty($data['value'])) {
                            $query->whereDate('requested_date', $data['value']);
                        }
                        return $query;
                    }),
                Filter::make('request_code')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('value')
                            ->label('Mã yêu cầu')
                            ->placeholder('Nhập mã yêu cầu'),
                    ])
                    ->query(function ($query, $data) {
                        if (!empty($data['value'])) {
                            $query->where('request_code', 'like', '%' . $data['value'] . '%');
                        }
                        return $query;
                    }),
                Filter::make('customer_name')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('value')
                            ->label('Tên khách hàng')
                            ->placeholder('Nhập tên khách hàng'),
                    ])
                    ->query(function ($query, $data) {
                        if (!empty($data['value'])) {
                            $query->where('customer_name', 'like', '%' . $data['value'] . '%');
                        }
                        return $query;
                    }),
            ])
            ->groups([
                Group::make('driver_name')
                    ->label('Tài xế'),
                    
            ])
            ->defaultSort('requested_date', 'desc')
            ->recordActions([
                // \Filament\Actions\Action::make('successfully')
                //     ->label('Hoàn thành')
                //     ->icon('heroicon-o-check-circle')
                //     ->color('success')
                //     ->visible(fn($record) => $record->status === ShippingRequestStatus::IN_PROGRESS)
                //     ->requiresConfirmation()
                //     ->action(function($record) {
                //         $record->status = 'completed';
                //         $record->items()->with('pallet','crate')->each(function ($item) {
                //             if ($item->crate) {
                //                 $item->crate->status = \App\Enums\CrateStatus::SHIPPED->value;
                //                 $item->crate->save();
                //             }
                //             if ($item->pallet) {
                //                 $item->pallet->status = \App\Enums\PalletStatus::SHIPPED->value;
                //                 $item->pallet->save();
                //             }
                //         });
                //         $record->save();
                //     }),
                // \Filament\Actions\Action::make('approve')
                //     ->label('Duyệt')
                //     ->icon('heroicon-o-check-circle')
                //     ->color('success')
                //     ->visible(fn($record) => $record->status === ShippingRequestStatus::PENDING)
                //     ->requiresConfirmation()
                //     ->action(function($record) {
                //         $record->status = 'in_progress';
                //         $record->save();
                //     }),
                // \Filament\Actions\Action::make('cancel')
                //     ->label('Hủy')
                //     ->icon('heroicon-o-x-circle')
                //     ->color('danger')
                //     ->visible(fn($record) => in_array($record->status, [ShippingRequestStatus::PENDING, ShippingRequestStatus::IN_PROGRESS]))
                //     ->requiresConfirmation()
                //     ->action(function($record) {
                //         $record->status = 'cancelled';
                //         $record->save();
                //     }),
                 EditAction::make()
                    ->icon('heroicon-m-pencil-square')
                    ->iconButton(),
                ], position: RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Xóa'),
                ]),
            ]);
    }
}
