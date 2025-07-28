<?php

namespace App\Http\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePalletRequest;
use App\Http\Requests\UpdatePalletRequest;
use App\Http\Resources\PalletResource;
use App\Models\Pallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Dedoc\Scramble\Support\OperationBuilder;
use Dedoc\Scramble\Support\RouteInfo;
use Illuminate\Support\Facades\Route;

class PalletsController extends Controller
{
    /**
     * Hiển thị danh sách các pallet.
     *
     * @param Request $request
     * @return JsonResource
     *
     * @operationId listPallets
     * @tags Pallets
     * @summary Lấy danh sách pallet có phân trang
     * 
     * @parameter query string search Tìm kiếm theo tên pallet
     * @parameter query string sort Trường để sắp xếp (mặc định: created_at)
     * @parameter query string direction Hướng sắp xếp (asc/desc)
     * @parameter query integer per_page Số lượng item trên mỗi trang (mặc định: 15)
     * @parameter query integer page Số trang hiện tại
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "pallet_id": "PLT001",
     *             "crate_id": "CRT001",
     *             "location_code": "A1-01",
     *             "status": "active",
     *             "checked_in_at": "2024-07-26T10:00:00Z",
     *             "checked_in_by": "John Doe",
     *             "checked_out_at": null,
     *             "checked_out_by": null,
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
        $sort = $request->query('sort', 'created_at');
        $direction = $request->query('direction', 'desc');
        $perPage = $request->query('per_page', 15);
        $page = $request->query('page', 1);

        $pallets = Pallet::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy($sort, $direction)
            ->paginate($perPage, ['*'], 'page', $page);

        return PalletResource::collection($pallets);
    }
    /**
     * Hiển thị thông tin chi tiết của một pallet.
     *
     * @param Pallet $pallet
     * @return JsonResource
     *
     * @operationId getPallet
     * @tags Pallets
     * @summary Lấy thông tin chi tiết của một pallet theo ID
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "pallet_id": "PLT001",
     *         "crate_id": "CRT001",
     *         "location_code": "A1-01",
     *         "status": "active",
     *         "checked_in_at": "2024-07-26T10:00:00Z",
     *         "checked_in_by": "John Doe",
     *         "checked_out_at": null,
     *         "checked_out_by": null,
     *         "created_at": "2024-07-26T10:00:00Z",
     *         "updated_at": "2024-07-26T10:00:00Z"
     *     }
     * }
     * @response 404 {
     *     "message": "Pallet not found"
     * }
     */
    public function show(Pallet $pallet): JsonResource
    {
        return new PalletResource($pallet);
    }

    /**
     * Tạo mới một pallet.
     *
     * @param StorePalletRequest $request
     * @return JsonResource
     *
     * @operationId createPallet
     * @tags Pallets
     * @summary Tạo mới một pallet
     *
     * @requestBody {
     *     "required": true,
     *     "content": {
     *         "application/json": {
     *             "schema": {
     *                 "type": "object",
     *                 "required": ["pallet_id", "crate_id", "location_code", "status"],
     *                 "properties": {
     *                     "pallet_id": {
     *                         "type": "string",
     *                         "maxLength": 255
     *                     },
     *                     "crate_id": {
     *                         "type": "string",
     *                         "maxLength": 255
     *                     },
     *                     "location_code": {
     *                         "type": "string",
     *                         "maxLength": 255
     *                     },
     *                     "status": {
     *                         "type": "string",
     *                         "maxLength": 50
     *                     },
     *                     "checked_in_at": {
     *                         "type": "string",
     *                         "format": "date-time",
     *                         "nullable": true
     *                     },
     *                     "checked_in_by": {
     *                         "type": "string",
     *                         "maxLength": 255,
     *                         "nullable": true
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
     *         "pallet_id": "PLT001",
     *         "crate_id": "CRT001",
     *         "location_code": "A1-01",
     *         "status": "active",
     *         "checked_in_at": "2024-07-26T10:00:00Z",
     *         "checked_in_by": "John Doe",
     *         "checked_out_at": null,
     *         "checked_out_by": null,
     *         "created_at": "2024-07-26T10:00:00Z",
     *         "updated_at": "2024-07-26T10:00:00Z"
     *     }
     * }
     * @response 422 {
     *     "message": "Dữ liệu không hợp lệ.",
     *     "errors": {
     *         "pallet_id": ["Mã pallet là bắt buộc."]
     *     }
     * }
     */
    public function store(StorePalletRequest $request): JsonResource
    {
        $pallet = Pallet::create($request->validated());
        return new PalletResource($pallet);
    }

    /**
     * Cập nhật thông tin của một pallet.
     *
     * @param UpdatePalletRequest $request
     * @param Pallet $pallet
     * @return JsonResource
     *
     * @operationId updatePallet
     * @tags Pallets
     * @summary Cập nhật thông tin của một pallet
     *
     * @requestBody {
     *     "required": true,
     *     "content": {
     *         "application/json": {
     *             "schema": {
     *                 "type": "object",
     *                 "properties": {
     *                     "pallet_id": {
     *                         "type": "string",
     *                         "maxLength": 255
     *                     },
     *                     "crate_id": {
     *                         "type": "string",
     *                         "maxLength": 255
     *                     },
     *                     "location_code": {
     *                         "type": "string",
     *                         "maxLength": 255
     *                     },
     *                     "status": {
     *                         "type": "string",
     *                         "maxLength": 50
     *                     },
     *                     "checked_in_at": {
     *                         "type": "string",
     *                         "format": "date-time",
     *                         "nullable": true
     *                     },
     *                     "checked_in_by": {
     *                         "type": "string",
     *                         "maxLength": 255,
     *                         "nullable": true
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
     *         "pallet_id": "PLT001",
     *         "crate_id": "CRT001",
     *         "location_code": "A1-01",
     *         "status": "active",
     *         "checked_in_at": "2024-07-26T10:00:00Z",
     *         "checked_in_by": "John Doe",
     *         "checked_out_at": null,
     *         "checked_out_by": null,
     *         "created_at": "2024-07-26T10:00:00Z",
     *         "updated_at": "2024-07-26T10:00:00Z"
     *     }
     * }
     * @response 404 {
     *     "message": "Pallet not found"
     * }
     * @response 422 {
     *     "message": "Dữ liệu không hợp lệ.",
     *     "errors": {
     *         "status": ["Trạng thái không hợp lệ."]
     *     }
     * }
     */
    public function update(UpdatePalletRequest $request, Pallet $pallet): JsonResource
    {
        $pallet->update($request->validated());
        return new PalletResource($pallet);
    }

    /**
     * Xóa một pallet.
     *
     * @param Pallet $pallet
     * @return JsonResponse
     *
     * @operationId deletePallet
     * @tags Pallets
     * @summary Xóa một pallet khỏi hệ thống
     *
     * @response 200 {
     *     "message": "Đã xóa pallet thành công"
     * }
     * @response 404 {
     *     "message": "Không tìm thấy pallet"
     * }
     */
    public function destroy(Pallet $pallet): JsonResponse
    {
        $pallet->delete();
        return response()->json(['message' => 'Pallet deleted successfully']);
    }

}