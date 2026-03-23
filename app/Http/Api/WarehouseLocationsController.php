<?php

namespace App\Http\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWarehouseLocationRequest;
use App\Http\Requests\UpdateWarehouseLocationRequest;
use App\Http\Resources\WarehouseLocationResource;
use App\Models\WarehouseLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseLocationsController extends Controller
{
    /**
     * Hiển thị danh sách các vị trí trong kho
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
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

        $locations = WarehouseLocation::query()
            ->when($search, function ($query, $search) {
                return $query->where('location_code', 'like', "%{$search}%");
            })
            ->orderBy($sort, $direction)
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'message' => 'Vị trí trong kho đã hoàn thành',
            'data' => WarehouseLocationResource::collection($locations)
        ], 200);
    }

    // /**
    //  * Hiển thị chi tiết một vị trí trong kho
    //  *
    //  * @param WarehouseLocation $warehouseLocation
    //  * @return JsonResource
    //  */
    // public function show(WarehouseLocation $warehouseLocation): JsonResource
    // {
    //     return new WarehouseLocationResource($warehouseLocation->load('pallets'));
    // }

    // /**
    //  * Tạo mới một vị trí trong kho
    //  *
    //  * @param StoreWarehouseLocationRequest $request
    //  * @return JsonResource
    //  */
    // public function store(StoreWarehouseLocationRequest $request): JsonResource
    // {
    //     $location = WarehouseLocation::create($request->validated());
    //     return new WarehouseLocationResource($location);
    // }

    // /**
    //  * Cập nhật thông tin của một vị trí trong kho
    //  *
    //  * @param UpdateWarehouseLocationRequest $request
    //  * @param WarehouseLocation $warehouseLocation
    //  * @return JsonResource
    //  */
    // public function update(UpdateWarehouseLocationRequest $request, WarehouseLocation $warehouseLocation): JsonResource
    // {
    //     $warehouseLocation->update($request->validated());
    //     return new WarehouseLocationResource($warehouseLocation);
    // }

    // /**
    //  * Xóa một vị trí trong kho
    //  *
    //  * @param WarehouseLocation $warehouseLocation
    //  * @return JsonResponse
    //  */
    // public function destroy(WarehouseLocation $warehouseLocation): JsonResponse
    // {
    //     if ($warehouseLocation->pallets()->count() > 0) {
    //         return response()->json([
    //             'message' => 'Không thể xóa vị trí đang chứa pallet'
    //         ], 422);
    //     }

    //     $warehouseLocation->delete();
    //     return response()->json(['message' => 'Đã xóa vị trí thành công']);
    // }
}
