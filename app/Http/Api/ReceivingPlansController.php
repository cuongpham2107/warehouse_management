<?php

namespace App\Http\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReceivingPlanResource;
use App\Models\ReceivingPlan;
use App\Enums\ReceivingPlanStatus;
use App\Enums\CrateStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceivingPlansController extends Controller
{
    /**
     * Hiển thị danh sách kế hoạch nhận hàng đang xử lý.
     */
    public function index(Request $request): JsonResource
    {
        $search = $request->query('search', '');
        $sort = $request->query('sort', 'created_at');
        $direction = $request->query('direction', 'desc');
        $perPage = $request->query('per_page', 15);
        $page = $request->query('page', 1);

        $plans = ReceivingPlan::query()
            ->where('status', ReceivingPlanStatus::IN_PROGRESS->value)
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('plan_code', 'like', "%{$search}%")
                        ->orWhere('license_plate', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction)
            ->paginate($perPage, ['*'], 'page', $page);

        return ReceivingPlanResource::collection($plans);
    }

    /**
     * Hiển thị thông tin chi tiết của kế hoạch nhận hàng đang xử lý.
     *
     * @response ReceivingPlanResource
     * @response 404 array{message: string}
     */
    public function show(ReceivingPlan $receivingPlan): JsonResource
    {
        abort_if($receivingPlan->status !== ReceivingPlanStatus::IN_PROGRESS, 404, 'Không tìm thấy kế hoạch nhận hàng đang xử lý');

        return new ReceivingPlanResource(
            $receivingPlan->load(['vendor', 'creator', 'crates'])
        );
    }

    /**
     * Hoàn thành kế hoạch nhận hàng.
     *
     * @response ReceivingPlanResource
     * @response 400 array{message: string}
     * @response 404 array{message: string}
     */
    public function update(Request $request, int $id): JsonResource
    {
        $receivingPlan = ReceivingPlan::find($id);
        if (!$receivingPlan) {
            abort(404, 'Không tìm thấy kế hoạch nhận hàng');
        }
        
        $allCratesStored = $receivingPlan->crates->every(function ($crate) {
            return $crate->status === CrateStatus::STORED;
        });

        if (!$allCratesStored) {
            abort(400, 'Danh sách các kiện hàng chưa được đưa vào kho nên chưa thể hoàn thành kế hoạch');
        }
        
        if ($receivingPlan->status !== ReceivingPlanStatus::IN_PROGRESS) {
            abort(400, 'Kế hoạch nhận hàng không trong trạng thái đang xử lý');
        }
        
        $receivingPlan->status = ReceivingPlanStatus::COMPLETED;
        $receivingPlan->save();
        
        return new ReceivingPlanResource($receivingPlan);
    }
}