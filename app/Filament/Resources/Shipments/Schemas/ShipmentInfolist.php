<?php

namespace App\Filament\Resources\Shipments\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

class ShipmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Thông tin lô hàng')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('shipment_code')
                                    ->label('Mã lô hàng')
                                    ->copyable()
                                    ->icon('heroicon-o-hashtag')
                                    ->badge()
                                    ->color('primary'),
                                TextEntry::make('status')
                                    ->label('Trạng thái')
                                    ->badge()
                                    ->color(fn($state) => $state instanceof \App\Enums\ShipmentStatus ? $state->getColor() : 'gray')
                                    ->icon(fn($state) => $state instanceof \App\Enums\ShipmentStatus ? $state->getIcon() : null)
                                    ->formatStateUsing(fn($state) => $state instanceof \App\Enums\ShipmentStatus ? $state->getLabel() : ($state ?? 'N/A')),
                                TextEntry::make('vehicle.license_plate')
                                    ->label('Biển số xe')
                                    ->icon('heroicon-o-truck')
                                    ->weight('bold'),
                                TextEntry::make('shippingRequest.request_code')
                                    ->label('Mã yêu cầu vận chuyển')
                                    ->icon('heroicon-o-document-text')
                                    ->weight('bold'),
                                TextEntry::make('departure_time')
                                    ->label('Thời gian khởi hành')
                                    ->icon('heroicon-o-clock')
                                    ->dateTime('d/m/Y H:i')
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('arrival_time')
                                    ->label('Thời gian đến')
                                    ->icon('heroicon-o-clock')
                                    ->dateTime('d/m/Y H:i')
                                    ->badge()
                                    ->color('info'),
                            ]),
                        TextEntry::make('notes')
                            ->label('Ghi chú')
                            ->icon('heroicon-o-document-text')
                            ->placeholder('Không có ghi chú')
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(2),

                Section::make('Thống kê')
                    ->schema([
                        TextEntry::make('total_crates')
                            ->label('Tổng số thùng')
                            ->icon('heroicon-o-cube')
                            ->numeric()
                            ->suffix(' thùng')
                            ->color('success'),
                        TextEntry::make('total_pieces')
                            ->label('Tổng số sản phẩm')
                            ->icon('heroicon-o-cube')
                            ->numeric()
                            ->suffix(' sản phẩm')
                            ->color('info'),
                        TextEntry::make('total_weight')
                            ->label('Tổng khối lượng')
                            ->icon('heroicon-o-scale')
                            ->numeric(decimalPlaces: 2)
                            ->suffix(' kg')
                            ->color('warning'),
                    ])
                    ->columnSpan(1),

                Section::make('Thông tin hệ thống')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('createdBy.name')
                                    ->label('Người tạo')
                                    ->icon('heroicon-o-user')
                                    ->placeholder('Không xác định'),
                                TextEntry::make('created_at')
                                    ->label('Ngày tạo')
                                    ->icon('heroicon-o-calendar')
                                    ->dateTime('d/m/Y H:i')
                                    ->badge()
                                    ->color('gray'),
                                TextEntry::make('updated_at')
                                    ->label('Cập nhật cuối')
                                    ->icon('heroicon-o-arrow-path')
                                    ->dateTime('d/m/Y H:i')
                                    ->since()
                                    ->badge()
                                    ->color('gray'),
                                IconEntry::make('pod_generated')
                                    ->label('POD được tạo')
                                    ->boolean(),
                                TextEntry::make('pod_file_path')
                                    ->label('Đường dẫn file POD')
                                    ->icon('heroicon-o-link'),
                            ]),
                    ])
                    ->columnSpan(3)
                    ->collapsible(),
            ]);
    }
}
