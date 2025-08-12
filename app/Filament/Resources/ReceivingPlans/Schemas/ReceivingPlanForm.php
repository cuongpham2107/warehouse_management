<?php

namespace App\Filament\Resources\ReceivingPlans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Enums\ReceivingPlanStatus;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Vendors\Schemas\VendorForm;
use Illuminate\Support\Facades\Auth;
use App\Models\ReceivingPlan;
use Filament\Forms\Components\DateTimePicker;

class ReceivingPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Tabs::make('ReceivingPlanTabs')
                    ->tabs([
                        \Filament\Schemas\Components\Tabs\Tab::make('Thông tin kế hoạch')
                            ->schema([
                                TextInput::make('plan_code')
                                    ->label('Mã kế hoạch')
                                    ->default(fn() => 'RP' . now()->format('Ymd') . '-' . str_pad((string) (ReceivingPlan::max('id') + 1), 4, '0', STR_PAD_LEFT))
                                    ->helperText('Hệ thống sẽ tự động tạo mã theo format')
                                    ->columnSpan(6),

                                Select::make('vendor_id')
                                    ->label('Nhà cung cấp')
                                    ->required()
                                    ->relationship(
                                        name: 'vendor',
                                        titleAttribute: 'vendor_name',
                                        modifyQueryUsing: fn(Builder $query) => $query->where('status', 'active')
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Chọn nhà cung cấp')
                                    ->columnSpan(6)
                                    ->createOptionForm(fn(Schema $schema) => VendorForm::configure($schema)),

                                DateTimePicker::make('plan_date')
                                    ->label('Ngày hàng đến')
                                    ->required()
                                    ->default(now())
                                    ->seconds(false)
                                    ->displayFormat('d/m/Y | H:i')
                                    ->timezone('Asia/Ho_Chi_Minh')
                                    ->locale('vi')
                                    ->placeholder('Chọn ngày hàng đến')
                                    ->columnSpan(3)
                                    ->prefixIcon('heroicon-o-calendar'),
                                DateTimePicker::make('arrival_date')
                                    ->label('Giờ hạ hàng')
                                    ->default(now())
                                    ->seconds(false)
                                    ->displayFormat('d/m/Y | H:i')
                                    ->timezone('Asia/Ho_Chi_Minh')
                                    ->locale('vi')
                                    ->placeholder('Chọn giờ hạ hàng')
                                    ->prefixIcon('heroicon-o-calendar')
                                    ->columnSpan(3),
                                Select::make('status')
                                    ->label('Trạng thái')
                                    ->required()
                                    ->options(ReceivingPlanStatus::getOptions())
                                    ->default(ReceivingPlanStatus::PENDING->value)
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(3),
                                Select::make('created_by')
                                    ->label('Người tạo')
                                    ->relationship('creator', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(fn() => Auth::id())
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText('Tự động gán người dùng hiện tại')
                                    ->columnSpan(3),
                                
                                TextInput::make('transport_garage')
                                    ->label('Nhà xe vận chuyển')
                                    ->placeholder('Nhập tên nhà xe')
                                    ->columnSpan(4),
                                TextInput::make('license_plate')
                                    ->label('Biển số xe')
                                    ->placeholder('Nhập biển số xe')
                                    ->columnSpan(4),
                                TextInput::make('vehicle_capacity')
                                    ->label('Tải trọng xe (tấn)')
                                    ->numeric()
                                    ->placeholder('Nhập tải trọng xe')
                                    ->columnSpan(4),
                                Textarea::make('notes')
                                    ->label('Ghi chú')
                                    ->rows(3)
                                    ->placeholder('Nhập ghi chú về kế hoạch')
                                    ->columnSpanFull(),

                            ])->columns(12),
                        \Filament\Schemas\Components\Tabs\Tab::make('Thông tin số lượng')
                            ->schema([

                                TextInput::make('total_crates')
                                    ->label('Tổng Quantity')
                                    ->numeric()
                                    ->readOnly()
                                    ->default(0)
                                    ->minValue(0)
                                    ->placeholder('Nhập tổng số kiện')
                                    ->suffixIcon('heroicon-o-cube')
                                    ->readOnly(),

                                TextInput::make('total_pcs')
                                    ->label('Tổng PCS')
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

                            ])->columns(3),
                    ])->columnSpanFull()
            ]);
    }
}
