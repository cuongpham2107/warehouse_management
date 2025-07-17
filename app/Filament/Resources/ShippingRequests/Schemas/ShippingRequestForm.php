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
use App\States\PendingState;
use App\States\ProcessingState;
use App\States\ReadyState;
use App\States\ShippedState;
use App\States\DeliveredState;
use App\States\CancelledState;
use Filament\Schemas\Components\Flex;

class ShippingRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Thông tin yêu cầu')
                    ->icon('heroicon-o-document-text')
                    ->description('Thông tin cơ bản về yêu cầu vận chuyển')
                    ->schema([
                        TextInput::make('request_code')
                            ->label('Mã yêu cầu')
                            ->required()
                            ->readOnly()
                            ->default(fn () => 'REQ-' . now()->format('YmdHis'))
                            ->placeholder('Nhập mã yêu cầu'),

                        DatePicker::make('requested_date')
                            ->label('Ngày yêu cầu')
                            ->default(now())
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
                            // ->disabled()
                            // ->dehydrated()
                            ->required()
                            ->options(fn () => \App\States\ShippingRequestState::getStateOptions())
                            ->default('pending')
                            ->native(false)
                            ->dehydrateStateUsing(fn($state) => \App\States\ShippingRequestState::getStateClass($state)),
                    ])
                    ->columns(2)
                    ->collapsible(),
                    Section::make('Thông tin khách hàng')
                        ->icon('heroicon-o-user')
                        ->description('Chi tiết về khách hàng và địa chỉ giao hàng')
                        ->schema([
                            TextInput::make('customer_name')
                                ->label('Tên khách hàng')
                                ->required()
                                ->placeholder('Nhập tên khách hàng'),

                            TextInput::make('customer_contact')
                                ->label('Thông tin liên hệ')
                                ->placeholder('Nhập thông tin liên hệ'),

                            Textarea::make('delivery_address')
                                ->label('Địa chỉ giao hàng')
                                ->rows(3)
                                ->required()
                                ->placeholder('Nhập địa chỉ giao hàng chi tiết')
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->collapsible(),

                    Section::make('Thông tin bổ sung')
                        ->icon('heroicon-o-document-text')
                        ->description('Ghi chú và thông tin người tạo')
                        ->schema([
                            Textarea::make('notes')
                                ->label('Ghi chú')
                                ->rows(3)
                                ->placeholder('Nhập ghi chú về yêu cầu'),

                            Select::make('created_by')
                                ->label('Người tạo')
                                ->options(fn () => \App\Models\User::pluck('name', 'id'))
                                ->default(optional(\Illuminate\Support\Facades\Auth::user())->id)
                                ->searchable()
                                ->preload()
                                ->placeholder('Chọn người tạo yêu cầu'),
                        ])
                        ->columns(1)
                        ->collapsible()
                        ->collapsed(true),
         
            ]);
    }
}
