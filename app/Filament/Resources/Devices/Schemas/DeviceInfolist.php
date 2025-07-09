<?php

namespace App\Filament\Resources\Devices\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DeviceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('device_code')
                    ->label('Mã thiết bị'),
                TextEntry::make('device_type')
                    ->label('Loại thiết bị'),
                TextEntry::make('device_name')
                    ->label('Tên thiết bị'),
                TextEntry::make('mac_address')
                    ->label('Địa chỉ MAC'),
                TextEntry::make('ip_address')
                    ->label('Địa chỉ IP'),
                TextEntry::make('status')
                    ->label('Trạng thái'),
                TextEntry::make('last_sync_at')
                    ->label('Lần đồng bộ cuối')
                    ->dateTime(),
                TextEntry::make('assigned_to')
                    ->label('Được gán cho')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime(),
            ]);
    }
}
