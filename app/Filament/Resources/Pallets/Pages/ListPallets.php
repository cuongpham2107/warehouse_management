<?php

namespace App\Filament\Resources\Pallets\Pages;

use App\Filament\Resources\Pallets\PalletResource;
use App\Exports\PalletExport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPallets extends ListRecords
{
    protected static string $resource = PalletResource::class;

    public function getTitle(): string
    {
        return 'Danh sách pallet';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tạo mới'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->with([
            'crate.receivingPlan',
            'shippingRequestItem.shippingRequest',
            'checkedInBy',
            'checkedOutBy'
        ]);
    }
}
