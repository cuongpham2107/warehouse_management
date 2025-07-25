<?php

namespace App\Filament\Resources\ReceivingPlans\RelationManagers;

use Exception;
use Livewire\Component;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\DateTimePicker;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use App\Filament\Resources\Crates\Imports\CratesExcelImport;
use App\Filament\Resources\Crates\CrateResource;
use App\Enums\PalletStatus;
use App\Enums\ReceivingPlanStatus;
use App\Models\Pallet;
use App\Models\WarehouseLocation;
use App\Models\ReceivingPlan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;

class CratesRelationManager extends RelationManager
{
    protected static string $relationship = 'crates';

    protected static ?string $title = 'Kiện hàng';

    protected static ?string $modelLabel = 'kiện hàng';

    protected static ?string $pluralModelLabel = 'kiện hàng';

    public function form(Schema $schema): Schema
    {
        return CrateResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return CrateResource::table($table)
            ->heading('📦 Danh sách kiện hàng')
            ->description('Quản lý các kiện hàng thuộc kế hoạch nhập kho này')
            ->headerActions([
                Action::make('ImportXlsx')
                    ->label('Nhập danh sách Crate ID từ Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->modalHeading('Import kiện hàng từ file Excel (.xlsx)')
                    ->modalDescription('Chọn file Excel để import kiện hàng vào kế hoạch nhập kho này')
                    ->schema([
                        Action::make('downloadExcelSample')
                            ->label('Tải mẫu Excel')
                            ->link()
                            ->url(route('samples.excel.crates'))
                            ->color('gray')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->openUrlInNewTab(),
                        FileUpload::make('xlsx_file')
                            ->label('File XLSX')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                            ->disk('public')
                            ->directory('imports')
                            ->required()
                            ->reorderable()
                            ->appendFiles()
                            ->moveFiles()
                            ->helperText('Tải lên file Excel (.xlsx) chứa dữ liệu các kiện hàng'), // 5MB
                    ])
                    ->action(function (array $data, Component $livewire): void {
                        $filePath = Storage::disk('public')->path($data['xlsx_file']);

                        try {
                            // Kiểm tra nếu là relationship manager (đang ở trong receiving plan)
                            if ($this->getOwnerRecord()) {
                                $receivingPlan = ReceivingPlan::find($this->getOwnerRecord()->id);

                                if (!$receivingPlan) {
                                    throw new Exception('Không tìm thấy kế hoạch nhập kho');
                                }
                            }
                            // Sử dụng Laravel Excel để import
                            $import = new CratesExcelImport();
                            if ($this->getOwnerRecord()->id) {
                                $import->setReceivingPlanId($this->getOwnerRecord()->id);
                            }
                            Excel::import($import, $filePath);

                            //Tính tổng số lượng kiện hàng đã import
                            $totalCrates = $import->getTotalCrates();
                            $totalPcs = $import->getTotalPcs();
                            $totalWeight = $import->getTotalWeight();

                            $receivingPlan->update([
                                'status' => ReceivingPlanStatus::IN_PROGRESS->value,
                                'total_crates' => $totalCrates,
                                'total_pcs' => $totalPcs,
                                'total_weight' => $totalWeight,
                            ]);
                            $livewire->dispatch('receivingPlan.refresh');

                            Notification::make()
                                ->success()
                                ->title('Import thành công')
                                ->body('
                                    Dữ liệu kiện hàng đã được import thành công. Đổi trạng thái kế hoạch sang "Đang thực hiện".
                                    Tính tổng số kiện hàng: ' . $totalCrates . ', sản phẩm: ' . $totalPcs . ', khối lượng: ' . $totalWeight . 'kg.
                                    ')
                                ->send();
                        } catch (Exception $e) {
                            // Thông báo lỗi
                            Notification::make()
                                ->danger()
                                ->title('Import thất bại')
                                ->body('Có lỗi xảy ra: ' . $e->getMessage())
                                ->send();
                        }
                    })
                    ->modalSubmitAction(fn (Action $action) => $action->label('Tải lên')),


                CreateAction::make()
                    ->label('Tạo kiện hàng mới')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->modalHeading('Tạo kiện hàng mới')
                    ->modalDescription('Thêm kiện hàng vào kế hoạch nhập kho này')
                    ->mutateDataUsing(function (array $data): array {
                        // Auto-set receiving_plan_id khi tạo từ RelationManager
                        $data['receiving_plan_id'] = $this->getOwnerRecord()->getKey();
                        return $data;
                    }),
                BulkAction::make('import_and_assign')
                    ->label('Nhập kho và gán vị trí')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->button()
                    ->outlined()
                    ->color('primary')
                    ->modalHeading('Nhập kho và gán vị trí cho kiện hàng')
                    ->modalDescription('Chọn vị trí kho để gán cho các kiện hàng đã chọn')
                    ->modalWidth(Width::SevenExtraLarge)
                    ->schema([
                        Repeater::make('pallets')
                            ->label("Thông tin kiện hàng")
                            ->table([
                                TableColumn::make('Mã pallet'),
                                TableColumn::make('Kiện hàng'),
                                TableColumn::make('Vị trí kho'),
                                TableColumn::make('Thời gian nhập kho')
                            ])
                            ->schema([
                                TextInput::make('pallet_id')
                                    ->required(),
                                TextInput::make('crate_code')
                                    ->readOnly()
                                    ->required(),
                                TextInput::make('location_code')
                                    ->required()
                                    ->datalist(
                                        fn () => WarehouseLocation::query()->pluck('location_code')->all()
                                    ),
                                DateTimePicker::make('checked_in_at')
                                    ->seconds(false)
                                    ->readOnly()
                                    ->required(),
                                Hidden::make('crate_id')
                            ])

                    ])
                    ->fillForm(function (Collection $collection) {
                        foreach ($collection as $crate) {
                            $crateData['pallets'][] = [
                                'pallet_id' => 'PALLET-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6)),
                                'crate_code' => $crate->crate_id,
                                'location_code' => '',
                                'checked_in_at' => now(),
                                'crate_id' => $crate->id,
                                'crate' => $crate
                            ];
                        }
                        return $crateData;
                    })
                    ->action(function (array $data, Component $livewire): void {
                           
                        try {
                            $pallets = $data['pallets'];
                            if (!$pallets || !is_array($pallets)) {
                                Notification::make()
                                    ->danger()
                                    ->title('Lỗi')
                                    ->body('Không có kiện hàng nào được chọn để nhập kho.')
                                    ->send();
                                return;
                            }
                            foreach ($pallets as $pallet) {
                                // Kiểm tra crate_id đã tồn tại trong pallet chưa
                                $exists = Pallet::where('crate_id', $pallet['crate_id'])->exists();
                                if ($exists) {
                                    // Có thể bỏ qua hoặc thông báo, ở đây sẽ bỏ qua
                                    Notification::make()
                                        ->warning()
                                        ->title('Cảnh báo')
                                        ->body('Kiện hàng ' . $pallet['crate_code'] . ' đã tồn tại trong pallet, bỏ qua.')
                                        ->send();
                                    continue;
                                }

                                $warehouseLocation = WarehouseLocation::where('location_code', $pallet['location_code'])->exists();
                                if (!$warehouseLocation) {
                                    WarehouseLocation::create([
                                        'location_code' => $pallet['location_code'],
                                    ]);
                                }

                                $pallet['crate']->update(['status' => 'stored']);

                                Pallet::create([
                                    'pallet_id' => $pallet['pallet_id'],
                                    'crate_id' => $pallet['crate_id'],
                                    'location_code' => $pallet['location_code'],
                                    'status' => PalletStatus::STORED->value,
                                    'checked_in_at' => $pallet['checked_in_at'],
                                    'checked_in_by' => Auth::id(),
                                ]);
                            }

                            //Cập nhập trạng thái kế hoạch nhập kho
                            $receivingPlan = $this->getOwnerRecord();
                            $receivingPlan->update(['status' => ReceivingPlanStatus::COMPLETED->value]);
                            $livewire->dispatch('receivingPlan.refresh');
                            Notification::make()
                                ->success()
                                ->title('Nhập kho thành công')
                                ->body('Các kiện hàng đã được nhập kho và gán vị trí thành công.')
                                ->actions([
                                    Action::make('Xem')
                                        ->button()
                                        ->url(route('filament.admin.resources.pallets.index', [
                                            'tableFilters' => [
                                                'receivingPlan' => [
                                                    'value' => $receivingPlan->id,
                                                ],
                                            ],
                                        ]))
                                ])
                                ->send();
                        } catch (Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Lỗi')
                                ->body('Có lỗi xảy ra khi nhập kho: ' . $e->getMessage())
                                ->send();
                            return;
                        }
                    })
                    ->modalSubmitAction(fn (Action $action) => $action->label('Lưu thông tin')),


                // BulkAction::make('choose_crate_export_warehouse')
                //     ->label('Chọn thùng hàng để xuất kho')
                //     ->icon('heroicon-o-check')
                //     ->color('primary')
                //     ->button()
                //     ->outlined()
                //     ->modalHeading('Chi tiết yêu cầu xuất kho')
                //     ->modalDescription('Vui lòng nhập các thông tin cần thiết để xuất kho các kiện hàng.')
                //     ->schema(fn($schema) => ShippingRequestForm::configure($schema))
                //     ->action(function (Collection $records, array $data) {
                //         $records = $records->select('id', 'pieces', 'status')->toArray();
                //         $allStored = collect($records)->every(function ($record) {
                //             $status = $record['status'];
                //             return ($status instanceof \App\Enums\CrateStatus ? $status->value : $status) === 'stored';
                //         });

                //         if (empty($records)) {
                //             Notification::make()
                //                 ->title('Không có thùng hàng nào được chọn')
                //                 ->body('Vui lòng chọn ít nhất một thùng hàng để xuất kho.')
                //                 ->danger()
                //                 ->send();
                //             return;
                //         }

                //         if (!$allStored) {
                //             Notification::make()
                //                 ->title('Không thể xuất kho')
                //                 ->body('Tất cả các kiện hàng được chọn phải ở trạng thái "Đã lưu kho" mới có thể xuất kho.')
                //                 ->danger()
                //                 ->send();
                //             return;
                //         }

                //         try {
                //             $shippingRequest = ShippingRequest::create($data);
                //             if (!$shippingRequest) {
                //                 Notification::make()
                //                     ->title('Lỗi khi tạo yêu cầu xuất kho')
                //                     ->body('Không thể tạo yêu cầu xuất kho. Vui lòng thử lại sau.')
                //                     ->danger()
                //                     ->send();
                //                 return;
                //             }
                //             foreach ($records as $record) {
                //                 $record->update(['status' => 'shipped']);
                //                 $shippingRequest->items()->create([
                //                     'crate_id' => $record['id'],
                //                     'quantity_requested' => $record['pieces'],
                //                     'status' => 'pending', // Đã tạo yêu cầu xuất kho, chưa xử lý
                //                 ]);
                //             }
                //             Notification::make()
                //                 ->title('Xuất kho thành công')
                //                 ->body("Đã tạo yêu cầu xuất {$shippingRequest->items()->count()} thùng hàng.")
                //                 ->success()
                //                 ->send();
                //         } catch (Exception $e) {
                //             Notification::make()
                //                 ->title('Lỗi khi xuất kho')
                //                 ->body($e->getMessage())
                //                 ->danger()
                //                 ->send();
                //             return;
                //         }
                //     })
                //     ->modalSubmitAction(fn (Action $action) => $action->label('Tạo yêu cầu xuất kho')),

            ])
            ->recordAction(null)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Xóa đã chọn')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Xóa các kiện hàng đã chọn')
                        ->modalDescription('Bạn có chắc muốn xóa tất cả kiện hàng đã chọn? Hành động này không thể hoàn tác.'),
                ]),

            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100]);
    }
}
