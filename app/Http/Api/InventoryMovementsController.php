<?php

namespace App\Http\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InventoryMovementResource;
use App\Http\Requests\StoreInventoryMovementRequest;
use App\Models\InventoryMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class InventoryMovementsController extends Controller
{
    /**
     * Hiển thị danh sách các di chuyển kho.
     *
     * @param Request $request
     * @return JsonResource
     *
     * @operationId listInventoryMovements
     * @tags Inventory Movements
     * @summary Lấy danh sách di chuyển kho có phân trang
     * 
     * @parameter query string search Tìm kiếm theo vị trí hoặc loại di chuyển
     * @parameter query string sort Trường để sắp xếp (mặc định: movement_date)
     * @parameter query string direction Hướng sắp xếp (asc/desc)
     * @parameter query integer per_page Số lượng item trên mỗi trang (mặc định: 15)
     * @parameter query integer page Số trang hiện tại
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "pallet_id": 1,
     *             "movement_type": "transfer",
     *             "from_location_code": "A1-01",
     *             "to_location_code": "B2-02",
     *             "movement_date": "2024-07-26T10:00:00Z",
     *             "notes": "Chuyển vị trí",
     *             "device_type": "scanner",
     *             "performer": {
     *                 "id": 1,
     *                 "name": "John Doe"
     *             },
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
        $search = $request->query('search', '');
        $sort = $request->query('sort', 'movement_date');
        $direction = $request->query('direction', 'desc');
        $perPage = $request->query('per_page', 15);
        $page = $request->query('page', 1);

        $movements = InventoryMovement::query()
            ->with(['pallet', 'performer'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('from_location_code', 'like', "%{$search}%")
                      ->orWhere('to_location_code', 'like', "%{$search}%")
                      ->orWhere('movement_type', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction)
            ->paginate($perPage, ['*'], 'page', $page);

        return InventoryMovementResource::collection($movements);
    }

    /**
     * Tạo di chuyển kho mới.
     *
     * @param StoreInventoryMovementRequest $request
     * @return JsonResource
     *
     * @operationId createInventoryMovement
     * @tags Inventory Movements
     * @summary Tạo mới một di chuyển kho
     *
     * @requestBody {
     *     "required": true,
     *     "content": {
     *         "application/json": {
     *             "schema": {
     *                 "type": "object",
     *                 "required": ["pallet_id", "movement_type", "from_location_code", "to_location_code", "movement_date", "device_type"],
     *                 "properties": {
     *                     "pallet_id": {
     *                         "type": "integer",
     *                         "description": "ID của pallet"
     *                     },
     *                     "movement_type": {
     *                         "type": "string",
     *                         "enum": ["transfer", "relocate"],
     *                         "description": "Loại di chuyển (transfer: Chuyển kho, relocate: Di chuyển vị trí)"
     *                     },
     *                     "from_location_code": {
     *                         "type": "string",
     *                         "description": "Mã vị trí nguồn"
     *                     },
     *                     "to_location_code": {
     *                         "type": "string",
     *                         "description": "Mã vị trí đích"
     *                     },
     *                     "movement_date": {
     *                         "type": "string",
     *                         "format": "date-time",
     *                         "description": "Thời gian di chuyển"
     *                     },
     *                     "notes": {
     *                         "type": "string",
     *                         "nullable": true,
     *                         "description": "Ghi chú"
     *                     },
     *                     "device_type": {
     *                         "type": "string",
     *                         "enum": ["scanner", "manual"],
     *                         "description": "Loại thiết bị thực hiện (scanner: Máy quét, manual: Thủ công)"
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
     *         "pallet_id": 1,
     *         "movement_type": "transfer",
     *         "from_location_code": "A1-01",
     *         "to_location_code": "B2-02",
     *         "movement_date": "2024-07-26T10:00:00Z",
     *         "notes": "Chuyển vị trí",
     *         "device_type": "scanner",
     *         "performer": {
     *             "id": 1,
     *             "name": "John Doe"
     *         },
     *         "created_at": "2024-07-26T10:00:00Z",
     *         "updated_at": "2024-07-26T10:00:00Z"
     *     }
     * }
     * @response 422 {
     *     "message": "Dữ liệu không hợp lệ",
     *     "errors": {
     *         "movement_type": ["Loại di chuyển phải là \"Chuyển kho\" hoặc \"Di chuyển vị trí\""],
     *         "device_type": ["Loại thiết bị phải là \"Máy quét\" hoặc \"Thủ công\""]
     *     }
     * }
     */
    public function store(StoreInventoryMovementRequest $request): JsonResource
    {
        $movement = InventoryMovement::create(array_merge(
            $request->validated(),
            ['performed_by' => Auth::id()]
        ));

        return new InventoryMovementResource($movement->load(['pallet', 'performer']));
    }

    /**
     * Hiển thị thông tin chi tiết của một di chuyển kho.
     *
     * @param InventoryMovement $inventoryMovement
     * @return JsonResource
     *
     * @operationId getInventoryMovement
     * @tags Inventory Movements
     * @summary Lấy thông tin chi tiết của một di chuyển kho
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "pallet_id": 1,
     *         "movement_type": "transfer",
     *         "from_location_code": "A1-01",
     *         "to_location_code": "B2-02",
     *         "movement_date": "2024-07-26T10:00:00Z",
     *         "notes": "Chuyển vị trí",
     *         "device_type": "scanner",
     *         "performer": {
     *             "id": 1,
     *             "name": "John Doe"
     *         },
     *         "created_at": "2024-07-26T10:00:00Z",
     *         "updated_at": "2024-07-26T10:00:00Z"
     *     }
     * }
     * @response 404 {
     *     "message": "Không tìm thấy bản ghi di chuyển"
     * }
     */
    public function show(InventoryMovement $inventoryMovement): JsonResource
    {
        return new InventoryMovementResource(
            $inventoryMovement->load(['pallet', 'performer'])
        );
    }
}
