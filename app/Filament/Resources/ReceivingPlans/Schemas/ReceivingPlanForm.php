<?php

namespace App\Filament\Resources\ReceivingPlans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Enums\ReceivingPlanStatus;
use App\Filament\Resources\Vehicles\Schemas\VehicleForm;
use App\Filament\Resources\Vendors\Schemas\VendorForm;
use Illuminate\Support\Facades\Auth;
use App\Models\ReceivingPlan;

class ReceivingPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Thông tin kế hoạch')
                    ->description('Thông tin cơ bản về kế hoạch nhập kho')
                    ->schema([
                        TextInput::make('plan_code')
                            ->label('Mã kế hoạch')
                            ->default(fn () => 'RP' . now()->format('Ymd') . '-' . str_pad((string) (ReceivingPlan::max('id') + 1), 4, '0', STR_PAD_LEFT))
                            ->helperText('Hệ thống sẽ tự động tạo mã theo format')
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(3),

                        Select::make('vendor_id')
                            ->label('Nhà cung cấp')
                            ->required()
                            ->relationship('vendor', 'vendor_name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Chọn nhà cung cấp')
                            ->columnSpan(3)
                            ->createOptionForm(fn (Schema $schema) => VendorForm::configure($schema)),

                        DatePicker::make('plan_date')
                            ->label('Ngày kế hoạch')
                            ->required()
                            ->default(now())
                            ->placeholder('Chọn ngày kế hoạch')
                            ->columnSpan(2),

                        Select::make('status')
                            ->label('Trạng thái')
                            ->required()
                            ->options(ReceivingPlanStatus::getOptions())
                            ->default(ReceivingPlanStatus::PENDING->value)
                            ->disabled()
                            ->dehydrated()
                            ->native(false)
                            ->columnSpan(2),
                         Select::make('created_by')
                            ->label('Người tạo')
                            ->relationship('creator', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn () => Auth::id())
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Tự động gán người dùng hiện tại')
                             ->columnSpan(2),
                        Textarea::make('notes')
                            ->label('Ghi chú')
                            ->rows(3)
                            ->placeholder('Nhập ghi chú về kế hoạch')
                            ->columnSpanFull(),

                        
                    ])
                    ->columns(6)
                    ->columnSpan(2),

                Section::make('Thông tin số lượng')
                    ->description('Số lượng và khối lượng sẽ được cập nhập sau khi import dữ liệu kiện hàng')
                    ->schema([
                        TextInput::make('total_crates')
                            ->label('Tổng số thùng')
                            ->numeric()
                            ->readOnly()
                            ->default(0)
                            ->minValue(0)
                            ->placeholder('Nhập tổng số thùng')
                            ->suffixIcon('heroicon-o-cube')
                            ->readOnly(),

                        TextInput::make('total_pieces')
                            ->label('Tổng số sản phẩm')
                            ->numeric()
                            ->readOnly()
                            ->default(0)
                            ->minValue(0)
                            ->placeholder('Nhập tổng số sản phẩm')
                            ->suffixIcon('heroicon-o-archive-box')
                             ->readOnly(),

                        TextInput::make('total_weight')
                            ->label('Tổng khối lượng (kg)')
                            ->numeric()
                            ->readOnly()
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('kg')
                            ->placeholder('Nhập tổng khối lượng'),
                    ])
                    ->columns(1)
                    ->columnSpan(1)
                    ->collapsible(),

              
            ]);
    }
}
