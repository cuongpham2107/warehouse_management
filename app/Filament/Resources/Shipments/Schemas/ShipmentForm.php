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
                \Filament\Schemas\Components\Tabs::make('Tabs')
                    ->tabs([
                        \Filament\Schemas\Components\Tabs\Tab::make('Thông tin lô hàng')
                            ->icon(\Filament\Support\Icons\Heroicon::Truck)
                            ->iconPosition(\Filament\Support\Enums\IconPosition::Before)
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
                            ->columns(2),
                        \Filament\Schemas\Components\Tabs\Tab::make('Thời gian vận chuyển')
                            ->icon(\Filament\Support\Icons\Heroicon::Clock)
                            ->iconPosition(\Filament\Support\Enums\IconPosition::Before)
                            ->schema([
                                DateTimePicker::make('departure_time')
                                    ->label('Thời gian khởi hành')
                                    ->placeholder('Chọn thời gian khởi hành'),
                                DateTimePicker::make('arrival_time')
                                    ->label('Thời gian đến')
                                    ->placeholder('Chọn thời gian đến'),
                            ])
                            ->columns(2),
                        \Filament\Schemas\Components\Tabs\Tab::make('Thông tin hàng hóa')
                            ->icon(\Filament\Support\Icons\Heroicon::Cube)
                            ->iconPosition(\Filament\Support\Enums\IconPosition::Before)
                            ->schema([
                                TextInput::make('vehicle.driver_name')
                                    ->label('Tên tài xế')
                                    ->readOnly(),
                                TextInput::make('vehicle.driver_phone')
                                    ->label('SĐT tài xế')
                                    ->readOnly(),
                            ])
                            ->columns(2),
                        \Filament\Schemas\Components\Tabs\Tab::make('Thông tin bổ sung')
                            ->icon(\Filament\Support\Icons\Heroicon::DocumentText)
                            ->iconPosition(\Filament\Support\Enums\IconPosition::Before)
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
                            ->columns(1),
                        \Filament\Schemas\Components\Tabs\Tab::make('Thông tin chi tiết')
                            ->icon(\Filament\Support\Icons\Heroicon::Scale)
                            ->iconPosition(\Filament\Support\Enums\IconPosition::Before)
                            ->schema([
                                TextInput::make('total_crates')
                                    ->label('Tổng số thùng')
                                    ->numeric()
                                    ->minValue(0),
                                TextInput::make('total_pieces')
                                    ->label('Tổng số sản phẩm')
                                    ->numeric()
                                    ->minValue(0),
                                TextInput::make('total_weight')
                                    ->label('Tổng khối lượng')
                                    ->numeric()
                                    ->minValue(0)
                                    ->suffix('kg'),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }
}
