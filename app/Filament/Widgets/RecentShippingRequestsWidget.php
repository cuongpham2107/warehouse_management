<?php

namespace App\Filament\Widgets;

use App\Models\ShippingRequest;
use App\Enums\ShippingRequestStatus;
use App\Enums\ShippingRequestPriority;
use App\States\PendingState;
use App\States\ProcessingState;
use App\States\ReadyState;
use App\States\CancelledState;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentShippingRequestsWidget extends BaseWidget
{
    protected static ?string $heading = 'Yêu cầu vận chuyển gần đây';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ShippingRequest::query()
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('request_code')
                    ->label('Mã yêu cầu')
                    ->searchable(),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Khách hàng')
                    ->searchable(),

                Tables\Columns\TextColumn::make('delivery_address')
                    ->label('Địa chỉ giao hàng')
                    ->limit(50),

                Tables\Columns\TextColumn::make('requested_date')
                    ->label('Ngày yêu cầu')
                    ->date(),

                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->label('Ưu tiên')
                    ->formatStateUsing(fn (ShippingRequestPriority $state): string => $state->getLabel())
                    ->color(fn (ShippingRequestPriority $state): string => $state->getColor())
                    ->icon(fn (ShippingRequestPriority $state): string => $state->getIcon()),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Trạng thái')
                    ->formatStateUsing(function ($state): string {
                        return match (get_class($state)) {
                            PendingState::class => 'Chờ xử lý',
                            ProcessingState::class => 'Đang xử lý',
                            ReadyState::class => 'Sẵn sàng',
                            CancelledState::class => 'Đã hủy',
                            default => 'Không xác định',
                        };
                    })
                    ->color(function ($state): string {
                        return match (get_class($state)) {
                            PendingState::class => 'warning',
                            ProcessingState::class => 'info',
                            ReadyState::class => 'primary',
                            CancelledState::class => 'danger',
                            default => 'gray',
                        };
                    })
                    ->icon(function ($state): string {
                        return match (get_class($state)) {
                            PendingState::class => 'heroicon-m-clock',
                            ProcessingState::class => 'heroicon-m-cog',
                            ReadyState::class => 'heroicon-m-check-circle',
                            CancelledState::class => 'heroicon-m-x-circle',
                            default => 'heroicon-m-question-mark-circle',
                        };
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tạo lúc')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}