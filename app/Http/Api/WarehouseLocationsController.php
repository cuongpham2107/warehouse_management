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
     * Hiển thị danh sách các vị trí trong kho.
     *
     * @param Request $request
     * @return JsonResource
     *
     * @operationId listLocations
     * @tags Warehouse Locations
     * @summary Lấy danh sách vị trí trong kho có phân trang
     * 
     * @parameter query string search Tìm kiếm theo mã vị trí
     * @parameter query string sort Trường để sắp xếp (mặc định: created_at)
     * @parameter query string direction Hướng sắp xếp (asc/desc)
     * @parameter query integer per_page Số lượng item trên mỗi trang (mặc định: 15)
     * @parameter query integer page Số trang hiện tại
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "location_code": "A1-01",
     *             "pallets_count": 5,
     *             "created_at": "2024-07-26T10:00:00Z",
     *             "updated_at": "2024-07-26T10:00:00Z"
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

        $locations = WarehouseLocation::query()
            ->when($search, function ($query, $search) {
                return $query->where('location_code', 'like', "%{$search}%");
            })
            ->orderBy($sort, $direction)
            ->paginate($perPage, ['*'], 'page', $page);

        return WarehouseLocationResource::collection($locations);
    }

    /**
     * Hiển thị chi tiết một vị trí trong kho.
     *
     * @param WarehouseLocation $warehouseLocation
     * @return JsonResource
     *
     * @operationId getLocation
     * @tags Warehouse Locations
     * @summary Lấy thông tin chi tiết của một vị trí trong kho
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "location_code": "A1-01",
     *         "pallets_count": 5,
     *         "created_at": "2024-07-26T10:00:00Z",
     *         "updated_at": "2024-07-26T10:00:00Z",
     *         "pallets": []
     *     }
     * }
     * @response 404 {
     *     "message": "Không tìm thấy vị trí"
     * }
     */
    public function show(WarehouseLocation $warehouseLocation): JsonResource
    {
        return new WarehouseLocationResource($warehouseLocation->load('pallets'));
    }

    /**
     * Tạo mới một vị trí trong kho.
     *
     * @param StoreWarehouseLocationRequest $request
     * @return JsonResource
     *
     * @operationId createLocation
     * @tags Warehouse Locations
     * @summary Tạo mới một vị trí trong kho
     *
     * @requestBody {
     *     "required": true,
     *     "content": {
     *         "application/json": {
     *             "schema": {
     *                 "type": "object",
     *                 "required": ["location_code"],
     *                 "properties": {
     *                     "location_code": {
     *                         "type": "string",
     *                         "maxLength": 255,
     *                         "description": "Mã vị trí trong kho"
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
     *         "location_code": "A1-01",
     *         "pallets_count": 0,
     *         "created_at": "2024-07-26T10:00:00Z",
     *         "updated_at": "2024-07-26T10:00:00Z"
     *     }
     * }
     * @response 422 {
     *     "message": "Dữ liệu không hợp lệ.",
     *     "errors": {
     *         "location_code": ["Mã vị trí là bắt buộc."]
     *     }
     * }
     */
    public function store(StoreWarehouseLocationRequest $request): JsonResource
    {
        $location = WarehouseLocation::create($request->validated());
        return new WarehouseLocationResource($location);
    }

    /**
     * Cập nhật thông tin của một vị trí trong kho.
     *
     * @param UpdateWarehouseLocationRequest $request
     * @param WarehouseLocation $warehouseLocation
     * @return JsonResource
     *
     * @operationId updateLocation
     * @tags Warehouse Locations
     * @summary Cập nhật thông tin của một vị trí trong kho
     *
     * @requestBody {
     *     "required": true,
     *     "content": {
     *         "application/json": {
     *             "schema": {
     *                 "type": "object",
     *                 "properties": {
     *                     "location_code": {
     *                         "type": "string",
     *                         "maxLength": 255,
     *                         "description": "Mã vị trí trong kho"
     *                     }
     *                 }
     *             }
     *         }
     *     }
     * }
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "location_code": "A1-01",
     *         "pallets_count": 0,
     *         "created_at": "2024-07-26T10:00:00Z",
     *         "updated_at": "2024-07-26T10:00:00Z"
     *     }
     * }
     * @response 404 {
     *     "message": "Không tìm thấy vị trí"
     * }
     * @response 422 {
     *     "message": "Dữ liệu không hợp lệ.",
     *     "errors": {
     *         "location_code": ["Mã vị trí này đã tồn tại."]
     *     }
     * }
     */
    public function update(UpdateWarehouseLocationRequest $request, WarehouseLocation $warehouseLocation): JsonResource
    {
        $warehouseLocation->update($request->validated());
        return new WarehouseLocationResource($warehouseLocation);
    }

    /**
     * Xóa một vị trí trong kho.
     *
     * @param WarehouseLocation $warehouseLocation
     * @return JsonResponse
     *
     * @operationId deleteLocation
     * @tags Warehouse Locations
     * @summary Xóa một vị trí khỏi kho
     *
     * @response 200 {
     *     "message": "Đã xóa vị trí thành công"
     * }
     * @response 404 {
     *     "message": "Không tìm thấy vị trí"
     * }
     * @response 422 {
     *     "message": "Không thể xóa vị trí đang chứa pallet"
     * }
     */
    public function destroy(WarehouseLocation $warehouseLocation): JsonResponse
    {
        if ($warehouseLocation->pallets()->count() > 0) {
            return response()->json([
                'message' => 'Không thể xóa vị trí đang chứa pallet'
            ], 422);
        }

        $warehouseLocation->delete();
        return response()->json(['message' => 'Đã xóa vị trí thành công']);
    }
}
