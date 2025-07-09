<?php

namespace App\Filament\Resources\Crates\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CrateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('crate_id')
                    ->label('Mã thùng hàng')
                    ->copyable(),
                    
                TextEntry::make('receivingPlan.plan_code')
                    ->label('Kế hoạch nhập kho'),
                    
                TextEntry::make('pieces')
                    ->label('Số lượng')
                    ->numeric(),
                    
                TextEntry::make('gross_weight')
                    ->label('Trọng lượng')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . ' kg'),
                    
                TextEntry::make('dimensions_length')
                    ->label('Chiều dài')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => $state . ' cm'),
                    
                TextEntry::make('dimensions_width')
                    ->label('Chiều rộng')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => $state . ' cm'),
                    
                TextEntry::make('dimensions_height')
                    ->label('Chiều cao')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => $state . ' cm'),
                    
                TextEntry::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'planned' => 'gray',
                        'received' => 'info',
                        'checked_in' => 'warning',
                        'stored' => 'success',
                        'shipped' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'planned' => 'Đã lên kế hoạch',
                        'received' => 'Đã nhận',
                        'checked_in' => 'Đã kiểm tra nhập kho',
                        'stored' => 'Đã lưu kho',
                        'shipped' => 'Đã xuất kho',
                        default => ucfirst($state),
                    }),
                    
                TextEntry::make('barcode')
                    ->label('Mã vạch')
                    ->copyable(),
                    
                TextEntry::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i'),
                    
                TextEntry::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime('d/m/Y H:i'),
            ]);
    }
}
