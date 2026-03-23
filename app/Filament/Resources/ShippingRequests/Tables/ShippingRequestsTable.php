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
use App\Enums\PalletStatus;
use Filament\Actions\DeleteAction;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Notifications\Notification;

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
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('transport_garage')
                    ->label('Nhà xe vận chuyển')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('vehicle_capacity')
                    ->label('Trọng tải xe (tấn)')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('license_plate')
                    ->label('Biển số xe')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('driver_name')
                    ->label('Tên tài xế')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('driver_phone')
                    ->label('SĐT tài xế')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('seal_number')
                    ->label('Số niêm phong')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('customer_name')
                    ->label('Tên khách hàng')
                    ->width('15%')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('customer_contact')
                    ->label('Liên hệ khách hàng')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('requested_date')
                    ->label('Ngày giao hàng')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('lifting_time')
                    ->label('Thời gian đóng hàng')
                    ->date('H:i')
                    ->alignCenter()
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
                            ->label('Ngày giao hàng (YYYY-MM-DD)')
                            ->placeholder('Nhập ngày giao hàng'),
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
                EditAction::make()
                    ->icon('heroicon-m-pencil-square')
                    ->iconButton(),
                DeleteAction::make()
                    ->icon('heroicon-m-trash')
                    ->iconButton()
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        // Cập nhật trạng thái pallet về "stored" trước khi xóa
                        $record->items()->with('pallet')->get()->each(function ($item) {
                            if ($item->pallet) {
                                $item->pallet->status = PalletStatus::STORED;
                                $item->pallet->save();
                            }
                        });

                        $record->delete();

                        Notification::make()
                            ->title('Yêu cầu vận chuyển đã được xóa thành công.')
                            ->success()
                            ->send();
                    }),
            ], position: RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Xóa'),
                ]),
            ])
            ->recordUrl(null);
    }
}
