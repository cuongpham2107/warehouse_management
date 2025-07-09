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
                TextEntry::make('pallet_id')
                    ->label('Mã pallet')
                    ->copyable(),
                    
                TextEntry::make('crate.crate_id')
                    ->label('Thùng hàng'),
                    
                TextEntry::make('location.location_code')
                    ->label('Vị trí'),
                    
                TextEntry::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in_transit' => 'warning',
                        'received' => 'info', 
                        'stored' => 'success',
                        'shipped' => 'danger',
                        'damaged' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'in_transit' => 'Đang vận chuyển',
                        'received' => 'Đã nhận',
                        'stored' => 'Đã lưu kho',
                        'shipped' => 'Đã xuất kho',
                        'damaged' => 'Bị hư hỏng',
                        default => ucfirst($state),
                    }),
                    
                TextEntry::make('checked_in_at')
                    ->label('Thời gian nhập kho')
                    ->dateTime('d/m/Y H:i'),
                    
                TextEntry::make('checkedInBy.name')
                    ->label('Người nhập kho'),
                    
                TextEntry::make('checked_out_at')
                    ->label('Thời gian xuất kho')
                    ->dateTime('d/m/Y H:i'),
                    
                TextEntry::make('checkedOutBy.name')
                    ->label('Người xuất kho'),
                    
                TextEntry::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i'),
                    
                TextEntry::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime('d/m/Y H:i'),
            ]);
    }
}
