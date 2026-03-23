<?php

namespace App\Filament\Resources\Vendors\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class VendorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                \Filament\Schemas\Components\Section::make('Thông tin nhà cung cấp')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                TextEntry::make('vendor_code')
                                    ->label('Mã nhà cung cấp')
                                    ->copyable()
                                    ->icon('heroicon-o-hashtag')
                                    ->badge()
                                    ->color('primary'),
                                TextEntry::make('vendor_name')
                                    ->label('Tên nhà cung cấp')
                                    ->icon('heroicon-o-building-storefront')
                                    ->weight('bold'),
                                TextEntry::make('contact_person')
                                    ->label('Người liên hệ')
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('phone')
                                    ->label('SĐT liên hệ')
                                    ->icon('heroicon-o-phone')
                                    ->copyable(),
                                TextEntry::make('email')
                                    ->label('Email')
                                    ->icon('heroicon-o-envelope')
                                    ->copyable(),
                                TextEntry::make('vendor_type')
                                    ->label('Loại nhà cung cấp')
                                    ->badge()
                                    ->color('info')
                                    ->icon(fn($state) => $state instanceof \App\Enums\VehicleType ? $state->icon() : \App\Enums\VehicleType::tryFrom($state)?->icon())
                                    ->formatStateUsing(fn($state) => $state instanceof \App\Enums\VehicleType ? $state->label() : \App\Enums\VehicleType::tryFrom($state)?->label() ?? $state),
                            ]),
                    ])
                    ->columnSpan(1),

                \Filament\Schemas\Components\Section::make('Trạng thái & hệ thống')
                    ->schema([
                        TextEntry::make('status')
                            ->label('Trạng thái')
                            ->badge()
                            ->color(fn($state) => $state instanceof \App\Enums\VehicleStatus ? $state->color() : \App\Enums\VehicleStatus::tryFrom($state)?->color() ?? 'gray')
                            ->icon(fn($state) => $state instanceof \App\Enums\VehicleStatus ? $state->icon() : \App\Enums\VehicleStatus::tryFrom($state)?->icon())
                            ->formatStateUsing(fn($state) => $state instanceof \App\Enums\VehicleStatus ? $state->label() : \App\Enums\VehicleStatus::tryFrom($state)?->label() ?? $state),
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
                    ])
                    ->columnSpan(1)
                    ->collapsible(),
            ]);
    }
}
