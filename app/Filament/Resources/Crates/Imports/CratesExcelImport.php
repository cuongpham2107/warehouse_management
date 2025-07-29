<?php

namespace App\Filament\Resources\Crates\Imports;

use App\Enums\CrateStatus;
use App\Models\Crate;
use App\Models\ReceivingPlan;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;

class CratesExcelImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    use Importable;
    
    protected ?int $receivingPlanId = null;

    public ?int $totalCrates = null;
    public ?int $totalPcs = null;
    public ?float $totalWeight = null;
    
    /**
     * Đặt receiving_plan_id khi import từ RelationManager
     */
    public function setReceivingPlanId(int $receivingPlanId): self
    {
        $this->receivingPlanId = $receivingPlanId;
        return $this;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Nếu đang import từ RelationManager, sử dụng receiving_plan_id đã đặt
        if ($this->receivingPlanId) {
            $receivingPlanId = $this->receivingPlanId;
        } else {
            // Nếu không, tìm receiving_plan_id từ plan_code trong file excel
            $receivingPlan = ReceivingPlan::where('plan_code', $row['receiving_plan_code'])->first();
            if (!$receivingPlan) {
                return null; // Skip this row if receiving plan not found
            }
            $receivingPlanId = $receivingPlan->id;
        }
        $this->totalCrates += 1;
        $this->totalPcs += $row['pcs'] ?? 0;
        $this->totalWeight += $row['gross_weight_kg'] ?? 0.0;

        // FirstOrNew to handle updates for existing crates
        return Crate::firstOrNew(
            ['crate_id' => $row['crate_id']],
            [
                'receiving_plan_id' => $receivingPlanId,
                'description' => $row['description_of_goods'] ?? null,
                'pieces' => $row['quantity'] ?? 0,
                'pcs' => $row['pcs'] ?? 0,
                'type' => $row['packing_type'],
                'gross_weight' => $row['gross_weight_kg'] ?? 0,
                'dimensions_length' => $row['dimensions_length_cm'] ?? 0,
                'dimensions_width' => $row['dimensions_width_cm'] ?? 0,
                'dimensions_height' => $row['dimensions_height_cm'] ?? 0,
                'status' => CrateStatus::from($row['status'] ?? CrateStatus::PLANNED->value),
                'barcode' => $row['barcode'] ?? null,
            ]
        );
    }

    public function getTotalCrates(): int
    {
        return $this->totalCrates ?? 0;
    }
    public function getTotalPcs(): int
    {
        return $this->totalPcs ?? 0;
    }
    public function getTotalWeight(): float
    {
        return $this->totalWeight ?? 0.0;
    }
   


    public function rules(): array
    {
        $rules = [
            'crate_id' => ['required', 'max:100'],
            'quantity' => ['required', 'integer', 'min:1'],
            'packing_type' => ['required', 'in:standard,box,pallet,crate'],
            'gross_weight_kg' => ['required', 'numeric', 'min:0'],
            'dimensions_length_cm' => ['nullable', 'numeric', 'min:0'],
            'dimensions_width_cm' => ['nullable', 'numeric', 'min:0'],
            'dimensions_height_cm' => ['nullable', 'numeric', 'min:0'],
            'description_of_goods' => ['nullable', 'max:1000'],
        ];
        
        // Nếu không import từ RelationManager, cần kiểm tra receiving_plan_code
        if (!$this->receivingPlanId) {
            $rules['receiving_plan_code'] = [
                'required',
                function ($attribute, $value, $fail) {
                    if (!ReceivingPlan::where('plan_code', $value)->exists()) {
                        $fail("Không tìm thấy kế hoạch nhập kho với mã: {$value}");
                    }
                }
            ];
        }
        
        return $rules;
    }
    public function batchSize(): int
    {
        return 10000; // Số lượng bản ghi mỗi lần import
    }
    public function chunkSize(): int
    {
        return 10000; // Số lượng bản ghi mỗi lần đọc
    }
}
