<?php

namespace App\Filament\Resources\ShippingRequests\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Enums\ShippingRequestStatus;
use Illuminate\Support\Facades\Date;

class ShippingRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make()
                    ->columns(2)
                    ->schema([
                        Section::make('Thông tin yêu cầu')
                            ->icon('heroicon-o-document-text')
                            ->description('Thông tin cơ bản về yêu cầu vận chuyển')
                            ->schema([
                                TextInput::make('request_code')
                                    ->label('Mã yêu cầu')
                                    ->required()
                                    ->default(fn() => 'REQ-' . now()->format('YmdHis'))
                                    ->placeholder('Nhập mã yêu cầu')
                                    ->columnSpan(1),
                                TextInput::make('seal_number')
                                    ->label('Số niêm phong')
                                    ->required()
                                    ->placeholder('Nhập số niêm phong')
                                    ->columnSpan(1),
                                DateTimePicker::make('requested_date')
                                    ->label('Ngày yêu cầu')
                                    ->default(now())
                                    ->required()
                                    ->placeholder('Chọn ngày yêu cầu')
                                    ->prefixIcon('heroicon-o-calendar')
                                    ->columnSpan(1),
                                DateTimePicker::make('lifting_time')
                                    ->label('Thời gian nâng hạ hàng')
                                    ->prefixIcon('heroicon-o-calendar')
                                    ->default(now())
                                    ->required()
                                    ->displayFormat('H:i d/m/Y')
                                    ->seconds(false)
                                    ->placeholder('Chọn thời gian nâng hạ hàng')
                                    ->columnSpan(1),
                            ])
                            ->columns(2)
                            ->columnSpan(1),
                        Section::make('Thông tin vận chuyển')
                            ->icon('heroicon-o-truck')
                            ->description('Thông tin về vận chuyển')
                            ->schema([
                                TextInput::make('transport_garage')
                                    ->label('Nhà xe vận chuyển')
                                    ->placeholder('Nhập tên nhà xe')
                                    ->columnSpan(1),
                                TextInput::make('license_plate')
                                    ->label('Biển số xe')
                                    ->required()
                                    ->placeholder('Nhập biển số xe')
                                    ->columnSpan(1),
                                TextInput::make('vehicle_capacity')
                                    ->label('Tải trọng xe (tấn)')
                                    ->numeric()
                                    ->placeholder('Nhập tải trọng xe')
                                    ->columnSpan(1),
                                DateTimePicker::make('departure_time')
                                    ->label('Thời gian xuất phát')
                                    ->prefixIcon('heroicon-o-calendar')
                                    ->default(now())
                                    ->displayFormat('H:i d/m/Y')
                                    ->seconds(false)
                                    ->required()
                                    ->placeholder('Chọn thời gian xuất phát')
                                    ->columnSpan(1),
                                TextInput::make('driver_name')
                                    ->label('Tên tài xế')
                                    ->placeholder('Nhập tên tài xế')
                                    ->nullable()
                                    ->columnSpan(1),
                                TextInput::make('driver_phone')
                                    ->label('Số điện thoại tài xế')
                                    ->placeholder('Nhập số điện thoại tài xế')
                                    ->nullable()
                                    ->columnSpan(1),
                            ])
                            ->columns(2)
                            ->columnSpan(1),
                        
                        Section::make('Thông tin khách hàng')
                            ->icon('heroicon-o-user')
                            ->description('Chi tiết về khách hàng và địa chỉ giao hàng')
                            ->schema([
                                TextInput::make('customer_name')
                                    ->label('Tên khách hàng')
                                    ->placeholder('Nhập tên khách hàng')
                                    ->columnSpan(1),
                                TextInput::make('customer_contact')
                                    ->label('Thông tin liên hệ')
                                    ->placeholder('Nhập thông tin liên hệ')
                                    ->columnSpan(1),
                                Textarea::make('delivery_address')
                                    ->label('Địa chỉ giao hàng')
                                    ->rows(3)
                                    ->placeholder('Nhập địa chỉ giao hàng chi tiết')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->columnSpan(1),
                        Section::make('Thông tin bổ sung')
                            ->icon('heroicon-o-document-text')
                            ->description('Ghi chú và thông tin người tạo')
                            ->schema([
                                Select::make('status')
                                    ->label('Trạng thái')
                                    ->options(ShippingRequestStatus::getOptions()),
                                Select::make('created_by')
                                    ->label('Người tạo')
                                    ->disabled()
                                    ->dehydrated()
                                    ->options(fn() => \App\Models\User::pluck('name', 'id'))
                                    ->default(optional(\Illuminate\Support\Facades\Auth::user())->id)
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Chọn người tạo yêu cầu'),
                                Textarea::make('notes')
                                    ->label('Ghi chú')
                                    ->rows(4)
                                    ->placeholder('Nhập ghi chú về yêu cầu')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->columnSpan(1),
                    ])
            ]);
    }
}
