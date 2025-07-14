<?php

namespace App\Filament\Resources\ReceivingPlans\Pages;

use App\Filament\Resources\ReceivingPlans\ReceivingPlanResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateReceivingPlan extends CreateRecord
{
    protected static string $resource = ReceivingPlanResource::class;

    public function getTitle(): string
    {
        return 'Tạo kế hoạch nhập kho';
    }

   
}
