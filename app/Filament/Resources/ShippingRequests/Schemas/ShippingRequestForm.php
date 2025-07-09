<?php

namespace App\Filament\Resources\ShippingRequests\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Enums\ShippingRequestPriority;
use App\Enums\ShippingRequestStatus;

class ShippingRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Thông tin yêu cầu')
                    ->description('Thông tin cơ bản về yêu cầu vận chuyển')
                    ->schema([
                        TextInput::make('request_code')
                            ->label('Mã yêu cầu')
                            ->required()
                            ->placeholder('Nhập mã yêu cầu'),
                            
                        DatePicker::make('requested_date')
                            ->label('Ngày yêu cầu')
                            ->required()
                            ->placeholder('Chọn ngày yêu cầu'),
                            
                        Select::make('priority')
                            ->label('Mức độ ưu tiên')
                            ->required()
                            ->options(ShippingRequestPriority::getOptions())
                            ->default(ShippingRequestPriority::MEDIUM->value)
                            ->native(false),
                            
                        Select::make('status')
                            ->label('Trạng thái')
                            ->required()
                            ->options(ShippingRequestStatus::getOptions())
                            ->default(ShippingRequestStatus::PENDING->value)
                            ->native(false),
                    ])
                    ->columns(2)
                    ->collapsible(),
                    
                Section::make('Thông tin khách hàng')
                    ->description('Chi tiết về khách hàng và địa chỉ giao hàng')
                    ->schema([
                        TextInput::make('customer_name')
                            ->label('Tên khách hàng')
                            ->required()
                            ->placeholder('Nhập tên khách hàng'),
                            
                        TextInput::make('customer_contact')
                            ->label('Liên hệ khách hàng')
                            ->placeholder('Nhập thông tin liên hệ'),
                            
                        Textarea::make('delivery_address')
                            ->label('Địa chỉ giao hàng')
                            ->rows(3)
                            ->placeholder('Nhập địa chỉ giao hàng chi tiết'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                    
                Section::make('Thông tin bổ sung')
                    ->description('Ghi chú và thông tin người tạo')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Ghi chú')
                            ->rows(3)
                            ->placeholder('Nhập ghi chú về yêu cầu'),
                            
                        Select::make('created_by')
                            ->label('Người tạo')
                            ->relationship('creator', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Chọn người tạo yêu cầu'),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }
}
