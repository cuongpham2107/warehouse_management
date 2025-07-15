<?php

namespace App\Filament\Resources\ShippingRequests\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use App\States\ShippingRequestState;

class ShippingRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin chính')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('request_code')
                                    ->label('Mã yêu cầu')
                                    ->copyable()
                                    ->icon('heroicon-m-clipboard-document-list')
                                    ->badge()
                                    ->color('primary'),

                                TextEntry::make('priority')
                                    ->label('Độ ưu tiên')
                                    ->badge()
                                    ->color(fn ($state) => $state instanceof \App\Enums\ShippingRequestPriority ? $state->getColor() : 'gray')
                                    ->icon(fn ($state) => $state instanceof \App\Enums\ShippingRequestPriority ? $state->getIcon() : null)
                                    ->formatStateUsing(fn ($state) => $state instanceof \App\Enums\ShippingRequestPriority ? $state->getLabel() : ($state ?? 'N/A')),

                                TextEntry::make('status')
                                    ->label('Trạng thái')
                                    ->badge()
                                    ->color(fn ($state) => $state instanceof ShippingRequestState ? $state->color() : 'gray')
                                    ->icon(fn ($state) => $state instanceof ShippingRequestState ? $state->icon() : null)
                                    ->formatStateUsing(fn ($state) => $state instanceof ShippingRequestState ? $state->label() : ($state ?? 'N/A')),

                                TextEntry::make('requested_date')
                                    ->label('Ngày yêu cầu')
                                    ->icon('heroicon-m-calendar-days')
                                    ->date('d/m/Y')
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('customer_name')
                                    ->label('Tên khách hàng')
                                    ->icon('heroicon-m-user')
                                    ->weight('bold'),

                                TextEntry::make('customer_contact')
                                    ->label('Liên hệ khách hàng')
                                    ->icon('heroicon-m-phone'),
                            ]),
                    ])
                    ->columnSpanFull(),

               

                Section::make('Thông tin hệ thống')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('creator.name')
                                    ->label('Người tạo')
                                    ->icon('heroicon-m-user')
                                    ->placeholder('Không xác định'),

                                TextEntry::make('created_at')
                                    ->label('Ngày tạo')
                                    ->icon('heroicon-m-clock')
                                    ->dateTime('d/m/Y H:i')
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('updated_at')
                                    ->label('Cập nhật cuối')
                                    ->icon('heroicon-m-arrow-path')
                                    ->dateTime('d/m/Y H:i')
                                    ->since()
                                    ->badge()
                                    ->color('gray'),
                            ]),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),

            ]);
    }
}
