<?php

namespace App\Filament\Resources\Devices\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use App\Enums\DeviceStatus;

class DeviceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                \Filament\Schemas\Components\Section::make('Thông tin thiết bị')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                TextEntry::make('device_code')
                                    ->label('Mã thiết bị')
                                    ->copyable()
                                    ->icon('heroicon-o-hashtag')
                                    ->badge()
                                    ->color('primary'),
                                TextEntry::make('device_type')
                                    ->label('Loại thiết bị')
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-cube'),
                                TextEntry::make('device_name')
                                    ->label('Tên thiết bị')
                                    ->icon('heroicon-o-device-phone-mobile')
                                    ->weight('bold'),
                                TextEntry::make('mac_address')
                                    ->label('Địa chỉ MAC')
                                    ->icon('heroicon-o-key')
                                    ->copyable(),
                                TextEntry::make('ip_address')
                                    ->label('Địa chỉ IP')
                                    ->icon('heroicon-o-globe-alt')
                                    ->copyable(),
                            ]),
                        TextEntry::make('notes')
                            ->label('Ghi chú')
                            ->icon('heroicon-o-document-text')
                            ->placeholder('Không có ghi chú')
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(2),

                \Filament\Schemas\Components\Section::make('Thông tin quản lý')
                    ->schema([
                        TextEntry::make('assigned_to')
                            ->label('Được gán cho')
                            ->icon('heroicon-o-user')
                            ->numeric()
                            ->color('info'),
                        TextEntry::make('status')
                            ->label('Trạng thái')
                            ->badge()
                            ->color(fn($state) => $state instanceof DeviceStatus ? $state->getColor() : DeviceStatus::tryFrom($state)?->getColor() ?? 'gray')
                            ->icon(fn($state) => $state instanceof DeviceStatus ? $state->getIcon() : DeviceStatus::tryFrom($state)?->getIcon())
                            ->formatStateUsing(fn($state) => $state instanceof DeviceStatus ? $state->getLabel() : DeviceStatus::tryFrom($state)?->getLabel() ?? $state),
                        TextEntry::make('last_sync_at')
                            ->label('Lần đồng bộ cuối')
                            ->icon('heroicon-o-arrow-path')
                            ->dateTime('d/m/Y H:i')
                            ->badge()
                            ->color('gray'),
                    ])
                    ->columnSpan(1),

                \Filament\Schemas\Components\Section::make('Thông tin hệ thống')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
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
                            ]),
                    ])
                    ->columnSpan(3)
                    ->collapsible(),
            ]);
    }
}
