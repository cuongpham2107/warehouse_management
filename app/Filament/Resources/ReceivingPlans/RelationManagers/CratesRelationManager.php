<?php

namespace App\Filament\Resources\ReceivingPlans\RelationManagers;

use App\Enums\PalletStatus;
use App\Enums\ReceivingPlanStatus;
use App\Filament\Resources\Crates\CrateResource;
use App\Models\ReceivingPlan;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use App\Filament\Resources\Crates\Imports\CratesExcelImport;
use App\Models\Pallet;
use App\Models\WarehouseLocation;
use Exception;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Auth;

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
                    ->label('Import Excel')
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
                            $totalPieces = $import->getTotalPieces();
                            $totalWeight = $import->getTotalWeight();

                            $receivingPlan->update([
                                'status' => ReceivingPlanStatus::IN_PROGRESS->value,
                                'total_crates' => $totalCrates,
                                'total_pieces' => $totalPieces,
                                'total_weight' => $totalWeight,
                            ]);
                            $livewire->dispatch('receivingPlan.refresh');

                            Notification::make()
                                ->success()
                                ->title('Import thành công')
                                ->body('
                                    Dữ liệu kiện hàng đã được import thành công. Đổi trạng thái kế hoạch sang "Đang thực hiện".
                                    Tính tổng số kiện hàng: ' . $totalCrates . ', sản phẩm: ' . $totalPieces . ', khối lượng: ' . $totalWeight . 'kg.
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
                    }),


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
                Action::make('import_and_assign')
                    ->label('Nhập kho và gán vị trí')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->button()
                    ->outlined()
                    ->color('success')
                    ->modalHeading('Nhập kho và gán vị trí cho kiện hàng')
                    ->modalDescription('Chọn vị trí kho để gán cho các kiện hàng đã chọn')
                    ->modalWidth(Width::SevenExtraLarge)
                    ->schema([
                        Select::make('location_id')
                            ->label('Áp dụng vị trí kho cho tất cả kiện hàng đã chọn')
                            ->options(WarehouseLocation::query()->pluck('location_code', 'id'))
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                $pallets = $get('pallets');
                                if ($pallets && is_array($pallets)) {
                                    foreach ($pallets as &$pallet) {
                                        $pallet['location_id'] = $state;
                                    }
                                    $set('pallets', $pallets);
                                }
                            })
                            ->required(),

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
                                    ->readOnly()
                                    ->required(),
                                TextInput::make('crate_code')
                                    ->readOnly()
                                    ->required(),
                               
                                Select::make('location_id')
                                    ->options(WarehouseLocation::query()->pluck('location_code', 'id'))
                                    ->searchable()
                                    ->required(),
                                DateTimePicker::make('checked_in_at')
                                    ->seconds(false)
                                    ->readOnly()
                                    ->required(),
                                Hidden::make('crate_id')
                            ])
                            ->default(function () {
                                $crates = $this->getOwnerRecord()->crates()->select('id', 'crate_id', 'created_at')->orderBy('crate_id', 'desc')->get();
                                foreach ($crates as $crate) {
                                    $crateData[] = [
                                        'pallet_id' => 'PALLET-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6)),
                                        'crate_code' => $crate->crate_id,
                                        'location_id' => '',
                                        'checked_in_at' => now(),
                                        'crate_id' => $crate->id,
                                    ];
                                }
                                return $crateData;
                            })
                    ])
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
                               Pallet::create(
                                    [
                                        'pallet_id' => $pallet['pallet_id'],
                                        'crate_id' => $pallet['crate_id'],
                                        'location_id' => intval($pallet['location_id']),
                                        'status' => PalletStatus::IN_TRANSIT->value,
                                        'checked_in_at' => $pallet['checked_in_at'],
                                        'checked_in_by' => Auth::id(),
                                    ]
                                );
                                // dd($test);
                            }
                            Notification::make()
                                ->success()
                                ->title('Nhập kho thành công')
                                ->body('Các kiện hàng đã được nhập kho và gán vị trí thành công.')
                                ->send();
                        } catch (Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Lỗi')
                                ->body('Có lỗi xảy ra khi nhập kho: ' . $e->getMessage())
                                ->send();
                            return;
                        }
                    }),


            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Xem')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn($record) => CrateResource::getUrl('view', ['record' => $record])),

                EditAction::make()
                    ->label('Sửa')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->url(fn($record) => CrateResource::getUrl('edit', ['record' => $record])),

                // DissociateAction::make()
                //     ->label('Hủy liên kết')
                //     ->icon('heroicon-o-x-mark')
                //     ->color('danger')
                //     ->requiresConfirmation()
                //     ->modalHeading('Hủy liên kết kiện hàng')
                //     ->modalDescription('Bạn có chắc muốn hủy liên kết kiện hàng này khỏi kế hoạch?')
                //     ->modalSubmitActionLabel('Hủy liên kết'),

                DeleteAction::make()
                    ->label('Xóa')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Xóa kiện hàng')
                    ->modalDescription('Bạn có chắc muốn xóa kiện hàng này? Hành động này không thể hoàn tác.')
                    ->modalSubmitActionLabel('Xóa'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make()
                        ->label('Hủy liên kết đã chọn')
                        ->icon('heroicon-o-x-mark')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Hủy liên kết các kiện hàng đã chọn')
                        ->modalDescription('Bạn có chắc muốn hủy liên kết tất cả kiện hàng đã chọn?'),

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
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
