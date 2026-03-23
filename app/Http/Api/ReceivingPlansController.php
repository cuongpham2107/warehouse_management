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
     * 1. Hiển thị danh sách kế hoạch nhận hàng đang xử lý
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        /**
         * Từ khoá tìm kiếm
         */
        $search = $request->query('search', '');
        /**
         * Sắp xếp theo trường
         */
        $sort = $request->query('sort', 'created_at');
        /**
         * Hướng sắp xếp
         */
        $direction = $request->query('direction', 'desc');
        /**
         * Số bản ghi trên mỗi trang
         */
        $perPage = $request->query('per_page', 15);
        /**
         * Trang hiện tại
         */
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

        return response()->json([
            'message' => 'Kế hoạch nhận hàng đang xử lý đã hoàn thành',
            'data' => ReceivingPlanResource::collection($plans)
        ], 200);
    }

    /**
     * 2. Hiển thị thông tin chi tiết của kế hoạch nhận hàng đang xử lý
     *
     * @param ReceivingPlan $receivingPlan ID của kế hoạch nhận hàng
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ReceivingPlan $receivingPlan)
    {
        abort_if($receivingPlan->status !== ReceivingPlanStatus::IN_PROGRESS, 404, 'Không tìm thấy kế hoạch nhận hàng đang xử lý');

        return response()->json([
            'message' => 'Kế hoạch nhận hàng đang xử lý đã hoàn thành',
            'data' => new ReceivingPlanResource($receivingPlan->load(['vendor', 'creator', 'crates']))
        ], 200);
    }

    /**
     * 5. Hoàn thành kế hoạch nhận hàng
     *
     * @param int $id  ID của kế hoạch nhận hàng
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
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
        
        return response()->json([
            'message' => 'Kế hoạch nhận hàng đã hoàn thành',
            'data' => new ReceivingPlanResource($receivingPlan)
        ], 200);
    }
}