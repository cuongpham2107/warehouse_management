<?php

namespace App\Filament\Resources\ReceivingPlans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Enums\ReceivingPlanStatus;

class ReceivingPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Thông tin kế hoạch')
                    ->description('Thông tin cơ bản về kế hoạch nhập kho')
                    ->schema([
                        TextInput::make('plan_code')
                            ->label('Mã kế hoạch')
                            ->required()
                            ->placeholder('Nhập mã kế hoạch'),
                            
                        Select::make('vendor_id')
                            ->label('Nhà cung cấp')
                            ->required()
                            ->relationship('vendor', 'vendor_name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Chọn nhà cung cấp'),
                            
                        DatePicker::make('plan_date')
                            ->label('Ngày kế hoạch')
                            ->required()
                            ->placeholder('Chọn ngày kế hoạch'),
                            
                        Select::make('status')
                            ->label('Trạng thái')
                            ->required()
                            ->options(ReceivingPlanStatus::getOptions())
                            ->default(ReceivingPlanStatus::PENDING->value)
                            ->native(false),
                    ])
                    ->columns(2)
                    ->collapsible(),
                    
                Section::make('Thông tin số lượng')
                    ->description('Số lượng và khối lượng dự kiến')
                    ->schema([
                        TextInput::make('total_crates')
                            ->label('Tổng số thùng')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->placeholder('Nhập tổng số thùng'),
                            
                        TextInput::make('total_pieces')
                            ->label('Tổng số sản phẩm')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->placeholder('Nhập tổng số sản phẩm'),
                            
                        TextInput::make('total_weight')
                            ->label('Tổng khối lượng (kg)')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('kg')
                            ->placeholder('Nhập tổng khối lượng'),
                    ])
                    ->columns(3)
                    ->collapsible(),
                    
                Section::make('Thông tin bổ sung')
                    ->description('Ghi chú và thông tin người tạo')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Ghi chú')
                            ->rows(3)
                            ->placeholder('Nhập ghi chú về kế hoạch'),
                            
                        Select::make('created_by')
                            ->label('Người tạo')
                            ->relationship('creator', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Chọn người tạo kế hoạch'),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }
}
