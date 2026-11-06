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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\DateTimePicker;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use App\Filament\Resources\Crates\Imports\CratesExcelImport;
use App\Filament\Resources\Crates\CrateResource;
use App\Enums\CrateStatus;
use App\Enums\PalletStatus;
use App\Enums\ReceivingPlanStatus;
use App\Models\Pallet;
use App\Models\WarehouseLocation;
use App\Models\ReceivingPlan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Schemas\Components\Section;


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
                                ]),

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

                BulkAction::make('bulk_edit_each')
                    ->label('Sửa hàng loạt')
                    ->icon('heroicon-o-pencil-square')
                    ->button()
                    ->outlined()
                    ->color('primary')
                    ->modalHeading('Sửa thông tin các kiện hàng đã chọn')
                    ->modalWidth(Width::SevenExtraLarge)
                    ->schema([
                        Repeater::make('crates')
                            ->label('Danh sách kiện hàng')
                            ->table([
                                TableColumn::make('Mã kiện'),
                                TableColumn::make('SL'),
                                TableColumn::make('PCS'),
                                TableColumn::make('Loại'),
                                TableColumn::make('Trọng lượng (kg)'),
                                TableColumn::make('Dài (cm)'),
                                TableColumn::make('Rộng (cm)'),
                                TableColumn::make('Cao (cm)'),
                                TableColumn::make('Trạng thái'),
                            ])
                            ->schema([
                                Hidden::make('id'),
                                TextInput::make('crate_id')
                                    ->label('Mã kiện')
                                    ->readOnly()
                                    ->required(),
                                TextInput::make('pieces')
                                    ->label('Số lượng')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0),
                                TextInput::make('pcs')
                                    ->label('PCS')
                                    ->numeric()
                                    ->minValue(0),
                                TextInput::make('type')
                                    ->label('Loại')
                                    ->required(),
                                TextInput::make('gross_weight')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->step(0.01),
                                TextInput::make('dimensions_length')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.1),
                                TextInput::make('dimensions_width')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.1),
                                TextInput::make('dimensions_height')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.1),
                                Select::make('status')
                                    ->label('Trạng thái')
                                    ->required()
                                    ->options(CrateStatus::getOptions()),
                                Textarea::make('description')
                                    ->label('Mô tả'),
                            ]),
                    ])
                    ->fillForm(function (Collection $collection) {
                        $crateData = [];
                        foreach ($collection as $crate) {
                            $crateData['crates'][] = [
                                'id' => $crate->id,
                                'crate_id' => $crate->crate_id,
                                'pieces' => $crate->pieces,
                                'pcs' => $crate->pcs,
                                'type' => $crate->type,
                                'gross_weight' => $crate->gross_weight,
                                'dimensions_length' => $crate->dimensions_length,
                                'dimensions_width' => $crate->dimensions_width,
                                'dimensions_height' => $crate->dimensions_height,
                                'status' => $crate->status instanceof CrateStatus ? $crate->status->value : $crate->status,
                                'description' => $crate->description,
                            ];
                        }
                        return $crateData;
                    })
                    ->action(function (array $data, Component $livewire): void {
                        $crates = $data['crates'] ?? [];
                        if (empty($crates)) {
                            Notification::make()
                                ->danger()
                                ->title('Lỗi')
                                ->body('Không có kiện hàng nào để cập nhật.')
                                ->send();
                            return;
                        }
                        try {
                            foreach ($crates as $crateData) {
                                \App\Models\Crate::where('id', $crateData['id'])->update([
                                    'pieces' => $crateData['pieces'],
                                    'pcs' => $crateData['pcs'],
                                    'type' => $crateData['type'],
                                    'gross_weight' => $crateData['gross_weight'],
                                    'dimensions_length' => $crateData['dimensions_length'] ?: null,
                                    'dimensions_width' => $crateData['dimensions_width'] ?: null,
                                    'dimensions_height' => $crateData['dimensions_height'] ?: null,
                                    'status' => $crateData['status'],
                                    'description' => $crateData['description'],
                                ]);
                            }
                            $livewire->dispatch('receivingPlan.refresh');
                            Notification::make()
                                ->success()
                                ->title('Cập nhật thành công')
                                ->body('Đã cập nhật thông tin cho ' . count($crates) . ' kiện hàng.')
                                ->send();
                        } catch (Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Lỗi cập nhật')
                                ->body('Có lỗi xảy ra: ' . $e->getMessage())
                                ->send();
                        }
                    })
                    ->modalSubmitAction(fn (Action $action) => $action->label('Lưu thay đổi')),

                BulkAction::make('bulk_edit_weight_dimensions')
                    ->label('Sửa trọng lượng & kích thước')
                    ->icon('heroicon-o-scale')
                    ->button()
                    ->outlined()
                    ->color('warning')
                    ->modalHeading('Sửa trọng lượng & kích thước hàng loạt')
                    ->modalDescription('Nhập giá trị mới, sẽ áp dụng cho tất cả kiện hàng đã chọn.')
                    ->schema([
                        TextInput::make('gross_weight')
                            ->label('Trọng lượng (kg)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('kg'),
                        TextInput::make('dimensions_length')
                            ->label('Chiều dài (cm)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->step(0.1)
                            ->suffix('cm'),
                        TextInput::make('dimensions_width')
                            ->label('Chiều rộng (cm)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->step(0.1)
                            ->suffix('cm'),
                        TextInput::make('dimensions_height')
                            ->label('Chiều cao (cm)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->step(0.1)
                            ->suffix('cm'),
                    ])
                    ->action(function (Collection $records, array $data, Component $livewire): void {
                        try {
                            $updateData = [
                                'gross_weight' => $data['gross_weight'],
                                'dimensions_length' => $data['dimensions_length'],
                                'dimensions_width' => $data['dimensions_width'],
                                'dimensions_height' => $data['dimensions_height'],
                            ];
                            foreach ($records as $crate) {
                                $crate->update($updateData);
                            }
                            $livewire->dispatch('receivingPlan.refresh');
                            Notification::make()
                                ->success()
                                ->title('Cập nhật thành công')
                                ->body('Đã cập nhật trọng lượng & kích thước cho ' . $records->count() . ' kiện hàng.')
                                ->send();
                        } catch (Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Lỗi cập nhật')
                                ->body('Có lỗi xảy ra: ' . $e->getMessage())
                                ->send();
                        }
                    })
                    ->modalSubmitAction(fn (Action $action) => $action->label('Áp dụng cho tất cả')),
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
            ])

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
