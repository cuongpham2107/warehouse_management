<?php

namespace App\Http\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReceivingPlanResource;
use App\Models\ReceivingPlan;
use App\Enums\ReceivingPlanStatus;
use App\Enums\CrateStatus;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceivingPlansController extends Controller
{
    /**
     * Hiển thị danh sách kế hoạch nhận hàng đang xử lý.
     *
     * @param Request $request
     * @return JsonResource
     *
     * @operationId listInProgressReceivingPlans
     * @tags Receiving Plans
     * @summary Lấy danh sách kế hoạch nhận hàng đang xử lý
     * 
     * @parameter query string search Tìm kiếm theo mã kế hoạch hoặc biển số xe
     * @parameter query string sort Trường để sắp xếp (mặc định: plan_date)
     * @parameter query string direction Hướng sắp xếp (asc/desc)
     * @parameter query integer per_page Số lượng item trên mỗi trang (mặc định: 15)
     * @parameter query integer page Số trang hiện tại
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "plan_code": "RP20240728001",
     *             "vendor": {
     *                 "id": 1,
     *                 "name": "Nhà cung cấp A"
     *             },
     *             "license_plate": "51A-12345",
     *             "plan_date": "2024-07-28",
     *             "total_crates": 100,
     *             "total_pcs": 1000,
     *             "total_weight": 5000.50,
     *             "status": "in_progress",
     *             "completion_percentage": 45,
     *             "notes": "Ghi chú",
     *             "creator": {
     *                 "id": 1,
     *                 "name": "John Doe"
     *             },
     *             "created_at": "2024-07-28T10:00:00Z",
     *             "updated_at": "2024-07-28T10:00:00Z"
     *         }
     *     ],
     *     "links": {},
     *     "meta": {}
     * }
     */
    public function index(Request $request): JsonResource
    {
       /**
         * Đây là một tham số truy vấn để tìm kiếm các chuyển động hàng tồn kho.
         * @example 
         * @default 
         */
        $search = $request->query('search', '');

        /**
         * Đây là một tham số truy vấn để xác định trường để sắp xếp.
         * @example created_at
         * @default created_at
         */
        $sort = $request->query('sort', 'created_at');
        /**
         * Đây là một tham số truy vấn để xác định hướng sắp xếp.
         * @example asc
         * @default desc
         */
        $direction = $request->query('direction', 'desc');
        /**
         * Đây là một tham số truy vấn để xác định số lượng mục trên mỗi trang.
         * @example 15
         * @default 15
         */
        $perPage = $request->query('per_page', 15);
        /**
         * Đây là một tham số truy vấn để xác định trang hiện tại.
         * @example 1
         * @default 1
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

        return ReceivingPlanResource::collection($plans);
    }

    /**
     * Tạo kế hoạch nhận hàng mới.
     *
     * @param StoreReceivingPlanRequest $request
     * @return JsonResource
     *
     * @operationId createReceivingPlan
     * @tags Receiving Plans
     * @summary Tạo mới kế hoạch nhận hàng
     *
     * @requestBody {
     *     "required": true,
     *     "content": {
     *         "application/json": {
     *             "schema": {
     *                 "type": "object",
     *                 "required": ["plan_code", "vendor_id", "license_plate", "plan_date", "total_crates"],
     *                 "properties": {
     *                     "plan_code": {
     *                         "type": "string",
     *                         "description": "Mã kế hoạch nhận hàng"
     *                     },
     *                     "vendor_id": {
     *                         "type": "integer",
     *                         "description": "ID của nhà cung cấp"
     *                     },
     *                     "license_plate": {
     *                         "type": "string",
     *                         "description": "Biển số xe"
     *                     },
     *                     "plan_date": {
     *                         "type": "string",
     *                         "format": "date",
     *                         "description": "Ngày nhận hàng"
     *                     },
     *                     "total_crates": {
     *                         "type": "integer",
     *                         "description": "Tổng số thùng"
     *                     },
     *                     "total_pcs": {
     *                         "type": "integer",
     *                         "nullable": true,
     *                         "description": "Tổng số lượng"
     *                     },
     *                     "total_weight": {
     *                         "type": "number",
     *                         "format": "float",
     *                         "nullable": true,
     *                         "description": "Tổng trọng lượng"
     *                     },
     *                     "notes": {
     *                         "type": "string",
     *                         "nullable": true,
     *                         "description": "Ghi chú"
     *                     }
     *                 }
     *             }
     *         }
     *     }
     * }
     *
     * @response 201 {
     *     "data": {
     *         "id": 1,
     *         "plan_code": "RP20240728001",
     *         "vendor": {
     *             "id": 1,
     *             "name": "Nhà cung cấp A"
     *         },
     *         "license_plate": "51A-12345",
     *         "plan_date": "2024-07-28",
     *         "total_crates": 100,
     *         "total_pcs": 1000,
     *         "total_weight": 5000.50,
     *         "status": "pending",
     *         "completion_percentage": 0,
     *         "notes": "Ghi chú",
     *         "creator": {
     *             "id": 1,
     *             "name": "John Doe"
     *         },
     *         "created_at": "2024-07-28T10:00:00Z",
     *         "updated_at": "2024-07-28T10:00:00Z"
     *     }
     * }
     * @response 422 {
     *     "message": "Dữ liệu không hợp lệ",
     *     "errors": {
     *         "plan_code": ["Mã kế hoạch nhận hàng đã tồn tại"],
     *         "vendor_id": ["Nhà cung cấp không tồn tại"],
     *         "total_crates": ["Tổng số thùng phải lớn hơn 0"]
     *     }
     * }
     */
    /**
     * Hiển thị thông tin chi tiết của kế hoạch nhận hàng đang xử lý.
     *
     * @param ReceivingPlan $receivingPlan
     * @return JsonResource
     *
     * @operationId getInProgressReceivingPlan
     * @tags Receiving Plans
     * @summary Lấy thông tin chi tiết của kế hoạch nhận hàng đang xử lý
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "plan_code": "RP20240728001",
     *         "vendor": {
     *             "id": 1,
     *             "name": "Nhà cung cấp A"
     *         },
     *         "license_plate": "51A-12345",
     *         "plan_date": "2024-07-28",
     *         "total_crates": 100,
     *         "total_pcs": 1000,
     *         "total_weight": 5000.50,
     *         "status": "in_progress",
     *         "completion_percentage": 45,
     *         "notes": "Ghi chú",
     *         "creator": {
     *             "id": 1,
     *             "name": "John Doe"
     *         },
     *         "created_at": "2024-07-28T10:00:00Z",
     *         "updated_at": "2024-07-28T10:00:00Z",
     *         "crates": [
     *             {
     *                 "id": 1,
     *                 "crate_code": "CR001",
     *                 "pcs": 10,
     *                 "gross_weight": 50.25,
     *                 "status": "checked_in"
     *             }
     *         ]
     *     }
     * }
     * @response 404 {
     *     "message": "Không tìm thấy kế hoạch nhận hàng đang xử lý"
     * }
     */
    public function show(ReceivingPlan $receivingPlan): JsonResource
    {
        abort_if($receivingPlan->status !== ReceivingPlanStatus::IN_PROGRESS, 404, 'Không tìm thấy kế hoạch nhận hàng đang xử lý');
        
        return new ReceivingPlanResource(
            $receivingPlan->load(['vendor', 'creator', 'crates'])
        );
    }


    /**
     * Update receiving plan status to completed.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResource
     * 
     * @operationId completeReceivingPlan
     * @tags Receiving Plans
     * @summary Complete a receiving plan
     * 
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "plan_code": "RP20240728001",
     *         "vendor": {
     *             "id": 1,
     *             "name": "Nhà cung cấp A"
     *         },
     *         "license_plate": "51A-12345", 
     *         "plan_date": "2024-07-28",
     *         "total_crates": 100,
     *         "total_pcs": 1000,
     *         "total_weight": 5000.50,
     *         "status": "completed",
     *         "completion_percentage": 100,
     *         "notes": "Ghi chú",
     *         "creator": {
     *             "id": 1,
     *             "name": "John Doe"
     *         },
     *         "created_at": "2024-07-28T10:00:00Z",
     *         "updated_at": "2024-07-28T10:00:00Z"
     *     }
     * }
     * @response 404 {
     *     "message": "Không tìm thấy kế hoạch nhận hàng"
     * }
     */
    public function update(Request $request, int $id): JsonResource 
    {
        $receivingPlan = ReceivingPlan::find($id);
        if(!$receivingPlan){
            abort(404,'Không tìm thấy kế hoạch nhận hàng');
        }
        $allCratesStored = $receivingPlan->crates->every(function($crate) {
            return $crate->status === CrateStatus::STORED;
        });

        if (!$allCratesStored) {
            abort(400, 'Danh sách các kiện hàng chưa được đưa vào kho nên chưa thể hoàn thành kế hoạch');
        }
        if($receivingPlan->status !== ReceivingPlanStatus::IN_PROGRESS){
            abort(400,'Kế hoạch nhận hàng không trong trạng thái đang xử lý');
        }
        $receivingPlan->status = ReceivingPlanStatus::COMPLETED;
        $receivingPlan->save();
        return new ReceivingPlanResource($receivingPlan);
    }
}
