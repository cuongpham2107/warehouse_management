<?php

namespace App\Filament\Widgets;

use App\Models\ShippingRequest;
use App\States\PendingState;
use App\States\ProcessingState;
use App\States\ReadyState;
use App\States\CancelledState;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentShippingRequestsWidget extends BaseWidget
{
    protected static ?string $heading = 'Phân loại yêu cầu vận chuyển gần đây';

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
                    ->label('Ngày giao hàng')
                    ->date(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tạo lúc')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}