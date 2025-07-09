<?php

namespace App\Filament\Resources\ReceivingPlans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ReceivingPlanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('plan_code')
                    ->label('Mã kế hoạch'),
                TextEntry::make('vendor_id')
                    ->label('Nhà cung cấp')
                    ->numeric(),
                TextEntry::make('plan_date')
                    ->label('Ngày kế hoạch')
                    ->date(),
                TextEntry::make('total_crates')
                    ->label('Tổng số thùng')
                    ->numeric(),
                TextEntry::make('total_pieces')
                    ->label('Tổng số sản phẩm')
                    ->numeric(),
                TextEntry::make('total_weight')
                    ->label('Tổng khối lượng')
                    ->numeric(),
                TextEntry::make('status')
                    ->label('Trạng thái'),
                TextEntry::make('created_by')
                    ->label('Người tạo')
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
