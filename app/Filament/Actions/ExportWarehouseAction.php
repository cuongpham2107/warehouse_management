<?php

namespace App\Filament\Actions;

use App\Enums\ShipmentStatus;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use App\Models\Shipment;
use App\States\ShippedState;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pallet;
use Filament\Forms\Components\Hidden;
use App\States\ShippingState;

class ExportWarehouseAction extends Action
{
    public static function make(?string $name = null): static
    {
        return parent::make($name)
            ->icon('heroicon-o-arrow-down-tray')
            ->label('Xuất kho')
            ->modal('export_warehouse_modal')
            ->modalHeading('Xuất kho yêu cầu vận chuyển')
            ->fillForm(function (Model $record) {
                        $crateData['shipment_code'] = 'DH-' . now()->format('YmdHis');
                        foreach ($record->items as $item) {
                            $pallet = Pallet::select('id','pallet_id','crate_id','location_id')->where('crate_id', $item->crate_id)->first();
                            $crateData['items'][] = [
                                'crate_id' => $item->crate->id,
                                'crate_code' => $item->crate->crate_id,
                                'pallet_id'=> $pallet->id,
                                'pallet_code' => $pallet->pallet_id,
                                'location_code' => $pallet->location->location_code ?? 'Chưa xác định',
                                'departure_time' => null,
                                'arrival_time' => null,
                                'quantity_requested' => $item->quantity_requested ?? 0,
                            ];
                        }
                        return $crateData;
                    })
            ->schema(
                fn(Schema $schema) =>
                $schema->columns(2)->components([
                    TextInput::make('shipment_code')
                        ->label('Mã vận chuyển')
                        ->required()
                        ->maxLength(50)
                        ->placeholder('Nhập mã vận chuyển')
                        ->disabled()
                        ->dehydrated()
                        ->columnSpanFull(),
                    Select::make('vehicle_id')
                        ->label('Xe tải')
                        ->options(fn() => \App\Models\Vehicle::all()->mapWithKeys(function ($vehicle) {
                            $label = sprintf(
                                'Mã xe: %s | Biển số: %s | Tải trọng: %s kg | Trạn thái: %s',
                                $vehicle->vehicle_code,
                                $vehicle->license_plate ?? '-',
                                $vehicle->capacity_weight ?? '-',
                                $vehicle->status->label()
                            );
                            return [$vehicle->id => $label];
                        }))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->placeholder('Chọn xe tải')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, callable $set) {
                            $set('driver_name', \App\Models\Vehicle::find($state)->driver_name ?? '');
                            $set('driver_phone', \App\Models\Vehicle::find($state)->driver_phone ?? '');
                        })
                        ->columnSpanFull(),
                    TextInput::make('driver_name')
                        ->label('Tên xe tải')
                        ->required()
                        ->maxLength(100)
                        ->placeholder('Nhập tên xe tải')
                        ->disabled(),
                    TextInput::make('driver_phone')
                        ->label('Số điện thoại tài xế')
                        ->required()
                        ->maxLength(15)
                        ->placeholder('Nhập số điện thoại tài xế')
                        ->disabled()
                        ->tel(),
                    DateTimePicker::make('departure_time')
                        ->label('Thời gian dự kiến xuất kho')
                        ->placeholder('Chọn thời dự kiến gian xuất kho')
                        ->seconds(false)
                        ->displayFormat('d/m/Y H:i'),
                    DateTimePicker::make('arrival_time')
                        ->label('Thời gian dự kiến nhận hàng')
                        ->placeholder('Chọn thời gian dự kiến nhận hàng')
                        ->seconds(false)
                        ->displayFormat('d/m/Y H:i')
                        ->default(null),
                    Textarea::make('notes')
                        ->label('Ghi chú')
                        ->rows(4)
                        ->maxLength(500)
                        ->columnSpanFull()
                        ->placeholder('Nhập ghi chú về yêu cầu vận chuyển'),
                    Repeater::make('items')
                        ->label("Thông tin kiện hàng")
                        ->addable(false)
                        ->deletable(false)
                        ->table([
                            TableColumn::make('Mã kiện hàng'),
                            TableColumn::make('Mã Pallet'),
                            TableColumn::make('Vị trí kho'),
                        ])
                        ->schema([
                            Hidden::make('crate_id')
                                ->required(),
                            TextInput::make('crate_code')
                                ->disabled()
                                ->required(),
                            Hidden::make('pallet_id')
                                ->required(),
                            TextInput::make('pallet_code')
                                ->disabled()
                                ->required(),
                            TextInput::make('location_code')
                                ->disabled()
                                ->required(),
                            TextInput::make('quantity_requested')
                                ->label('Số lượng yêu cầu')
                                ->numeric()
                                ->required()
                                ->default(0)
                                ->placeholder('Nhập số lượng yêu cầu')
                                ->columnSpanFull(),
                        ])
                        ->columnSpanFull()
                ])
            )
            ->action(function ($record, array $data) {

                if ($record->items->isEmpty()) {
                    Notification::make()
                        ->title('Không có kiện hàng để xuất kho')
                        ->body('Yêu cầu vận chuyển này không có kiện hàng nào để xuất kho.')
                        ->danger()
                        ->send();
                    return;
                }

                if (!$record->canExport()) {
                    Notification::make()
                        ->title('Không thể xuất kho yêu cầu vận chuyển')
                        ->body('Yêu cầu vận chuyển này không thể xuất kho vì trạng thái hiện tại không cho phép.')
                        ->danger()
                        ->send();
                    return;
                }

                try {
                    $shipment = Shipment::create([
                        'shipment_code' =>  'DH-' . now()->format('YmdHis'),
                        'vehicle_id' => $data['vehicle_id'],
                        'notes' => $data['notes'],
                        'shipping_request_id' => $record->id,
                        'departure_time' => $data['departure_time'],
                        'arrival_time' => $data['arrival_time'],
                        'total_crates' => $record->items->count(),
                        'total_pieces' => $record->items->sum(fn($item) => $item->crate?->pieces ?? 0),
                        'total_weight' => $record->items->sum(fn($item) => $item->crate?->gross_weight ?? 0),
                        'status' => ShipmentStatus::LOADING->value, 
                        'created_by' => Auth::user()->id,
                    ]);
                    // Tạo danh sách ShipmentItem từ các item của $data['items']
                    collect($data['items'])->each(function ($item) use ($shipment) {
                        $shipment->shipmentItems()->create([
                            'crate_id' => $item['crate_id'],
                            'pallet_id' => $item['pallet_id'],
                            'quantity' => $item['quantity_requested'] ?? 0,
                            'loaded_at' => now(),
                            'loaded_by' => Auth::user()->id,
                        ]);
                    });

                    //Cập nhập trạng thái yêu cầu vận chuyển
                    $record->update(['status' => ShippingState::class]);

                    //Cập nhập trạng thái cho các item
                    $record->items->each(fn($item) => $item->update([
                        'quantity_shipped' => $item->quantity_requested,
                    ]));



                    Notification::make()
                        ->title('Xuất kho yêu cầu vận chuyển thành công')
                        ->body('Yêu cầu xuất kho đã được tạo thành công.')
                        ->icon('heroicon-o-document-text')
                        ->iconColor('success')
                        ->success()
                        ->seconds(10)
                        ->actions([
                            Action::make('view')
                                ->label('Xem yêu cầu vận chuyển')
                                ->url(route('filament..resources.shipments.view', ['record' => $shipment->id]))
                                ->openUrlInNewTab()
                                ->icon('heroicon-o-eye')
                                ->color('primary'),
                        ])
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Lỗi khi xuất kho yêu cầu vận chuyển')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                    return;
                }
            });
    }
}
