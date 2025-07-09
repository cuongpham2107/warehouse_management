<?php

namespace App\Filament\Resources\ReceivingPlans\Pages;

use App\Filament\Resources\ReceivingPlans\ReceivingPlanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReceivingPlan extends CreateRecord
{
    protected static string $resource = ReceivingPlanResource::class;

    public function getTitle(): string
    {
        return 'Tạo kế hoạch nhập kho';
    }
}
