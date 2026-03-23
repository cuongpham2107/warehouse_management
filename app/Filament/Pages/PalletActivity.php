<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;
use UnitEnum;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PalletActivity extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.pallet-activity';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-cube';

    protected static string | UnitEnum | null $navigationGroup = '4. Hàng hóa';
    protected static ?int $navigationSort = 4;

    protected ?string $heading = 'Lịch sử hoạt động Pallet';

    public static function getNavigationLabel(): string
    {
        return 'Lịch sử hoạt động Pallet';
    }
    public static function getModelLabel(): string
    {
        return 'Lịch sử hoạt động Pallet';
    }

 
    public static function getPluralModelLabel(): string
    {
        return 'Lịch sử hoạt động Pallet';
    }

     public function table(Table $table): Table
    {
        return $table
            ->query(\App\Models\PalletActivity::query())
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Không có hoạt động nào')
            ->emptyStateDescription('Hiện tại không có hoạt động nào được ghi nhận cho pallet')
            ->columns([
                TextColumn::make('pallet.pallet_id')
                    ->label('Mã Pallet')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('action')
                    ->label('Hành động')
                    ->alignCenter()
                    ->badge()
                    ->color(fn($state) => $state->getColor())
                    ->icon(fn($state) => $state->getIcon())
                    ->formatStateUsing(fn($state) => $state->getLabel())
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('description')
                    ->label('Mô tả')
                    ->searchable()
                    ->width('30%')
                    ->wrap()
                    ->limit(50)
                    ->toggleable(),
                TextColumn::make('action_time')
                    ->label('Thời gian thực hiện')                  
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label('Người thực hiện')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),               

                TextColumn::make('old_data')
                    ->label('Dữ liệu cũ')
                    ->searchable()      
                    ->toggleable(),
                TextColumn::make('new_data')
                    ->label('Dữ liệu mới')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->groups([
                Group::make('pallet.pallet_id')
                    ->label('Pallet')
                    ->collapsible(),
                Group::make('user.name')
                    ->label('Người thực hiện')
                    ->collapsible(),
            ])
            ->defaultGroup('pallet.pallet_id')
            ->filters([
                SelectFilter::make('activity_type')
                    ->options([
                        'checkin' => 'Check-in',
                        'checkout' => 'Check-out',
                        'update' => 'Cập nhật',
                    ]),
                SelectFilter::make('user')
                    ->label('Nhân viên thực hiện')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('month_year')
                    ->label('Tháng/Năm')
                    ->form([
                        Select::make('month')
                            ->label('Tháng')
                            ->options([
                                1 => 'Tháng 1',
                                2 => 'Tháng 2', 
                                3 => 'Tháng 3',
                                4 => 'Tháng 4',
                                5 => 'Tháng 5',
                                6 => 'Tháng 6',
                                7 => 'Tháng 7',
                                8 => 'Tháng 8',
                                9 => 'Tháng 9',
                                10 => 'Tháng 10',
                                11 => 'Tháng 11',
                                12 => 'Tháng 12',
                            ])
                            ->placeholder('Chọn tháng'),
                        Select::make('year')
                            ->label('Năm')
                            ->options(function () {
                                $currentYear = now()->year;
                                $years = [];
                                for ($i = $currentYear - 2; $i <= $currentYear + 1; $i++) {
                                    $years[$i] = $i;
                                }
                                return $years;
                            })
                            ->placeholder('Chọn năm'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['month'],
                                fn (Builder $query, $month): Builder => $query->whereMonth('action_time', $month)
                            )
                            ->when(
                                $data['year'],
                                fn (Builder $query, $year): Builder => $query->whereYear('action_time', $year)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        
                        if ($data['month']) {
                            $monthName = [
                                1 => 'Tháng 1', 2 => 'Tháng 2', 3 => 'Tháng 3', 4 => 'Tháng 4',
                                5 => 'Tháng 5', 6 => 'Tháng 6', 7 => 'Tháng 7', 8 => 'Tháng 8',
                                9 => 'Tháng 9', 10 => 'Tháng 10', 11 => 'Tháng 11', 12 => 'Tháng 12'
                            ];
                            $indicators[] = 'Tháng: ' . $monthName[$data['month']];
                        }
                        
                        if ($data['year']) {
                            $indicators[] = 'Năm: ' . $data['year'];
                        }
                        
                        return $indicators;
                    }),
                Filter::make('date_range')
                    ->label('Khoảng thời gian')
                    ->form([
                        DatePicker::make('date_from')
                            ->label('Từ ngày')
                            ->placeholder('Chọn ngày bắt đầu'),
                        DatePicker::make('date_to')
                            ->label('Đến ngày')
                            ->placeholder('Chọn ngày kết thúc'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('action_time', '>=', $date)
                            )
                            ->when(
                                $data['date_to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('action_time', '<=', $date)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        
                        if ($data['date_from']) {
                            $indicators[] = 'Từ: ' . Carbon::parse($data['date_from'])->format('d/m/Y');
                        }
                        
                        if ($data['date_to']) {
                            $indicators[] = 'Đến: ' . Carbon::parse($data['date_to'])->format('d/m/Y');
                        }
                        
                        return $indicators;
                    }),
            ])
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(25)
            ->reorderableColumns();
        }
    
}
