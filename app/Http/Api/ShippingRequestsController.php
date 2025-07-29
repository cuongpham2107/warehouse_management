<?php

namespace App\Http\Api;

use App\Enums\ShippingRequestStatus;
use App\Enums\CrateStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\PalletResource;
use App\Http\Resources\ShippingRequestResource;
use App\Models\ShippingRequest;
use App\Models\Crate;
use App\Models\Pallet;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Support\Facades\Auth;

class ShippingRequestsController extends Controller
{
    /**
     * Hiển thị danh sách các Lệnh xuất hàng
     *
     * @param Request $request
     * @return JsonResource
     *
     */
    #[QueryParameter('search', description: 'Tìm kiếm theo mã yêu cầu hoặc tên khách hàng', type: 'string', required: false)]
    
    #[QueryParameter('sort', description: 'Trường để sắp xếp (mặc định: created_at)', type: 'string', required: false)]
    #[QueryParameter('direction', description: 'Hướng sắp xếp (asc/desc)', type: 'string', required: false)]
    #[QueryParameter('per_page', description: 'Số lượng item trên mỗi trang (mặc định: 15)', type: 'integer', required: false)]
    #[QueryParameter('page', description: 'Số trang hiện tại', type: 'integer', required: false)]
    public function index(Request $request): JsonResource
    {
        $search = $request->query('search', '');
        $sort = $request->query('sort', 'created_at');
        $direction = $request->query('direction', 'desc');
        $perPage = $request->query('per_page', 15);
        $page = $request->query('page', 1);

        $requests = ShippingRequest::query()
            ->with(['creator'])
            ->where('status',ShippingRequestStatus::IN_PROGRESS->value)
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('request_code', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction)
            ->paginate($perPage, ['*'], 'page', $page);

        return ShippingRequestResource::collection($requests);
    }
    /**
     * Hiển thị chi tiết của một yêu cầu vận chuyển
     *
     * @param ShippingRequest $shippingRequest
     * @return JsonResource
     */
    public function show(ShippingRequest $shippingRequest): JsonResource
    {
        return new ShippingRequestResource($shippingRequest->load(['creator', 'items']));
    }
    /**
     * Cập nhật trạng thái cho Yêu cầu xuất hàng 
     *
     * @param Request $request
     * @param ShippingRequest $shippingRequest
     * @return JsonResource
     */
    public function update(Request $request, ShippingRequest $shippingRequest): JsonResource
    {
        $allPalletsShipped = $shippingRequest->items()
            ->with('pallet')
            ->get()
            ->every(function ($item): bool {
                return $item->pallet && $item->pallet->status === \App\Enums\PalletStatus::SHIPPED;
            });
        if (!$allPalletsShipped) {
            return new JsonResource([
                'message' => 'Tất cả pallets của yêu cầu xuất hàng phải đã được vận chuyển',
            ]);
        }
        if($shippingRequest->status == ShippingRequestStatus::COMPLETED->value){
            return new JsonResource([
                'message' => 'Yêu cầu xuất hàng đã hoàn thành'
            ]);
        }

        $shippingRequest->update([
            'status' => ShippingRequestStatus::COMPLETED->value
        ]);
        return new ShippingRequestResource($shippingRequest->load(['creator', 'items']));
    }

    /**
     * Cập nhật trạng thái cho Kiện hàng và Pallet khi đã xuất hàng
     *
     * @param Request $request
     * @return JsonResource
     */
    public function checkOutPallet(Request $request): JsonResource
    {
        $request->validate([
            'crate_code' => 'required|string',
            'pallet_code' => 'required|string',
        ]);

        $crate = Crate::where('crate_id', $request->crate_code)->first();
        if($crate->status === \App\Enums\PalletStatus::SHIPPED) {
            return new JsonResource([
                'message' => 'Kiện hàng đã được vận chuyển'
            ]);
        }
         $pallet = Pallet::where('pallet_id', $request->pallet_code)->first();
        if($pallet->status === \App\Enums\PalletStatus::SHIPPED) {
            return new JsonResource([
                'message' => 'Pallet đã được xuất kho'
            ]);
        }


        $crate->status = CrateStatus::SHIPPED->value;
        $crate->save();
        $pallet->status = \App\Enums\PalletStatus::SHIPPED->value;
        $pallet->save();

        return new PalletResource($pallet->load(['crate']));
    }
   
}
