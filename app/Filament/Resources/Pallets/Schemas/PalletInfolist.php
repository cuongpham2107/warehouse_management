<?php

namespace App\Filament\Resources\Pallets\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PalletInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Section: Thông tin chính
                \Filament\Schemas\Components\Section::make('Thông tin pallet')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                TextEntry::make('pallet_id')
                                    ->label('Mã pallet')
                                    ->copyable()
                                    ->icon('heroicon-m-clipboard-document-list')
                                    ->badge()
                                    ->color('primary'),
                                TextEntry::make('crate.crate_id')
                                    ->label('Thùng hàng')
                                    ->icon('heroicon-m-cube')
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('location.location_code')
                                    ->label('Vị trí')
                                    ->icon('heroicon-m-map-pin')
                                    ->badge()
                                    ->color('success'),
                                TextEntry::make('status')
                                    ->label('Trạng thái')
                                    ->badge()
                                    ->color(fn ($state) => $state instanceof \App\Enums\PalletStatus ? $state->getColor() : 'gray')
                                    ->icon(fn ($state) => $state instanceof \App\Enums\PalletStatus ? $state->getIcon() : null)
                                    ->formatStateUsing(fn ($state) => $state instanceof \App\Enums\PalletStatus ? $state->getLabel() : ($state ?? 'N/A')),
                            ]),
                    ])
                    ->columnSpanFull(),

                // Section: Thông tin nhập/xuất kho
                \Filament\Schemas\Components\Section::make('Lịch sử nhập/xuất kho')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                TextEntry::make('checked_in_at')
                                    ->label('Thời gian nhập kho')
                                    ->dateTime('d/m/Y H:i')
                                    ->icon('heroicon-m-arrow-down-tray'),
                                TextEntry::make('checkedInBy.name')
                                    ->label('Người nhập kho')
                                    ->icon('heroicon-m-user'),
                                TextEntry::make('checked_out_at')
                                    ->label('Thời gian xuất kho')
                                    ->dateTime('d/m/Y H:i')
                                    ->icon('heroicon-m-arrow-up-tray'),
                                TextEntry::make('checkedOutBy.name')
                                    ->label('Người xuất kho')
                                    ->icon('heroicon-m-user'),
                            ]),
                    ])
                    ->columnSpanFull(),

                // Section: Thông tin hệ thống
                \Filament\Schemas\Components\Section::make('Thông tin hệ thống')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Ngày tạo')
                                    ->dateTime('d/m/Y H:i')
                                    ->icon('heroicon-m-clock')
                                    ->badge()
                                    ->color('gray'),
                                TextEntry::make('updated_at')
                                    ->label('Ngày cập nhật')
                                    ->dateTime('d/m/Y H:i')
                                    ->icon('heroicon-m-arrow-path')
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
