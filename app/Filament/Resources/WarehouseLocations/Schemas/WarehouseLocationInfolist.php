<?php

namespace App\Filament\Resources\WarehouseLocations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WarehouseLocationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('location_code')
                    ->label('Mã vị trí'),
                TextEntry::make('zone')
                    ->label('Khu vực'),
                TextEntry::make('rack')
                    ->label('Giá kệ'),
                TextEntry::make('level')
                    ->label('Tầng')
                    ->numeric(),
                TextEntry::make('position')
                    ->label('Vị trí'),
                TextEntry::make('max_weight')
                    ->label('Trọng lượng tối đa')
                    ->numeric(),
                TextEntry::make('max_volume')
                    ->label('Thể tích tối đa')
                    ->numeric(),
                TextEntry::make('status')
                    ->label('Trạng thái'),
                TextEntry::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime(),
            ]);
    }
}
