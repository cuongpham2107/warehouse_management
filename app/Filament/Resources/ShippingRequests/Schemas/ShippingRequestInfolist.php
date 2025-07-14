<?php

namespace App\Filament\Resources\ShippingRequests\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ShippingRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
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
                                    ->color(fn ($state) => match ($state) {
                                        'high' => 'danger',
                                        'medium' => 'warning',
                                        'low' => 'info',
                                        default => 'gray',
                                    }),

                                TextEntry::make('status')
                                    ->label('Trạng thái')
                                    ->badge()
                                    ->color(fn ($state) => match ($state) {
                                        'pending' => 'warning',
                                        'approved' => 'info',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn ($state) => match ($state) {
                                        'pending' => 'Chờ xử lý',
                                        'approved' => 'Đã duyệt',
                                        'completed' => 'Hoàn thành',
                                        'cancelled' => 'Đã hủy',
                                        default => $state,
                                    }),

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
                    ->columnSpan(2),

                Section::make('Thống kê')
                    ->schema([
                        TextEntry::make('total_items')
                            ->label('Tổng số sản phẩm')
                            ->icon('heroicon-m-squares-2x2')
                            ->numeric()
                            ->suffix(' sản phẩm')
                            ->color('info'),
                        TextEntry::make('total_weight')
                            ->label('Tổng khối lượng')
                            ->icon('heroicon-m-scale')
                            ->numeric(decimalPlaces: 2)
                            ->suffix(' kg')
                            ->color('warning'),
                    ])
                    ->columnSpan(1),

                Section::make('Thông tin hệ thống')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('created_by')
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
                    ->columnSpan(3)
                    ->collapsible(),
            ]);
    }
}
