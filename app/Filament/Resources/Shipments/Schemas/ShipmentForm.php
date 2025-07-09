<?php

namespace App\Filament\Resources\Shipments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Enums\ShipmentStatus;

class ShipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Thông tin lô hàng')
                    ->description('Thông tin cơ bản về lô hàng vận chuyển')
                    ->schema([
                        TextInput::make('shipment_code')
                            ->label('Mã lô hàng')
                            ->required()
                            ->placeholder('Nhập mã lô hàng'),
                            
                        Select::make('vehicle_id')
                            ->label('Xe tải')
                            ->relationship('vehicle', 'license_plate')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('Chọn xe tải'),
                            
                        Select::make('shipping_request_id')
                            ->label('Yêu cầu vận chuyển')
                            ->relationship('shippingRequest', 'request_code')
                            ->searchable()
                            ->preload()
                            ->placeholder('Chọn yêu cầu vận chuyển'),
                            
                        Select::make('status')
                            ->label('Trạng thái')
                            ->required()
                            ->options(ShipmentStatus::getOptions())
                            ->default(ShipmentStatus::LOADING)
                            ->native(false),
                    ])
                    ->columns(2)
                    ->collapsible(),
                    
                Section::make('Thời gian vận chuyển')
                    ->description('Thời gian khởi hành và đến nơi')
                    ->schema([
                        DateTimePicker::make('departure_time')
                            ->label('Thời gian khởi hành')
                            ->placeholder('Chọn thời gian khởi hành'),
                            
                        DateTimePicker::make('arrival_time')
                            ->label('Thời gian đến')
                            ->placeholder('Chọn thời gian đến'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                    
                Section::make('Thông tin hàng hóa')
                    ->description('Thông tin tài xế và ghi chú')
                    ->schema([
                        TextInput::make('driver_name')
                            ->label('Tên tài xế')
                            ->placeholder('Nhập tên tài xế'),
                            
                        TextInput::make('driver_phone')
                            ->label('SĐT tài xế')
                            ->tel()
                            ->placeholder('Nhập số điện thoại tài xế'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                    
                Section::make('Thông tin bổ sung')
                    ->description('Ghi chú và thông tin người tạo')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Ghi chú')
                            ->rows(3)
                            ->placeholder('Nhập ghi chú về lô hàng'),
                            
                        Select::make('created_by')
                            ->label('Người tạo')
                            ->relationship('createdBy', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Chọn người tạo lô hàng'),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }
}
