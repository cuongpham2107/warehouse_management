<?php

namespace App\Filament\Resources\ShippingRequests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use App\Enums\ShippingRequestPriority;
use App\Enums\ShippingRequestStatus;
use App\States\ShippingRequestState;

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
            ->recordActions([
                ViewAction::make()
                    ->label('Xem'),
                EditAction::make()
                    ->label('Chỉnh sửa'),
                \Filament\Actions\Action::make('approve')
                    ->label('Duyệt')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function($record) {
                        $record->status = 'ready';
                        $record->save();
                    }),
                \Filament\Actions\Action::make('cancel')
                    ->label('Hủy')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => in_array($record->status, ['pending','processing','ready']))
                    ->requiresConfirmation()
                    ->action(function($record) {
                        $record->status = 'cancelled';
                        $record->save();
                    }),
                \Filament\Actions\Action::make('generate_pick_list')
                    ->label('Tạo Pick List')
                    ->icon('heroicon-o-list-bullet')
                    ->color('primary')
                    ->visible(fn($record) => $record->status === 'ready')
                    ->requiresConfirmation()
                    ->action(function($record) {
                        // TODO: Logic tạo pick list ở đây
                        \Filament\Notifications\Notification::make()
                            ->title('Pick List đã được tạo!')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Xóa'),
                ]),
            ]);
    }
}
