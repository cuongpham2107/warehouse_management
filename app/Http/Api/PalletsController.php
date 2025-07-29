<?php

namespace App\Http\Api;

use App\Enums\PalletStatus;
use App\Enums\CrateStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePalletRequest;
use App\Http\Requests\UpdatePalletRequest;
use App\Http\Resources\PalletResource;
use App\Models\Pallet;
use App\Models\Crate;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PalletsController extends Controller
{
    // /**
    //  * Hiển thị danh sách các pallet.
    //  *
    //  * @param Request $request
    //  * @return JsonResource
    //  *
    //  * @operationId listPallets
    //  * @tags Pallets
    //  * @summary Lấy danh sách pallet có phân trang
    //  * 
    //  * @parameter query string search Tìm kiếm theo tên pallet
    //  * @parameter query string sort Trường để sắp xếp (mặc định: created_at)
    //  * @parameter query string direction Hướng sắp xếp (asc/desc)
    //  * @parameter query integer per_page Số lượng item trên mỗi trang (mặc định: 15)
    //  * @parameter query integer page Số trang hiện tại
    //  *
    //  * @response 200 {
    //  *     "data": [
    //  *         {
    //  *             "id": 1,
    //  *             "pallet_id": "PLT001",
    //  *             "crate_id": "CRT001",
    //  *             "location_code": "A1-01",
    //  *             "status": "active",
    //  *             "checked_in_at": "2024-07-26T10:00:00Z",
    //  *             "checked_in_by": "John Doe",
    //  *             "checked_out_at": null,
    //  *             "checked_out_by": null,
    //  *             "created_at": "2024-07-26T10:00:00Z",
    //  *             "updated_at": "2024-07-26T10:00:00Z"
    //  *         }
    //  *     ],
    //  *     "links": {},
    //  *     "meta": {}
    //  * }
    //  */
    // public function index(Request $request): JsonResource
    // {
    //     /**
    //      * Đây là một tham số truy vấn để tìm kiếm các chuyển động hàng tồn kho.
    //      * @example 
    //      * @default 
    //      */
    //     $search = $request->query('search', '');

    //     /**
    //      * Đây là một tham số truy vấn để xác định trường để sắp xếp.
    //      * @example created_at
    //      * @default created_at
    //      */
    //     $sort = $request->query('sort', 'created_at');
    //     /**
    //      * Đây là một tham số truy vấn để xác định hướng sắp xếp.
    //      * @example asc
    //      * @default desc
    //      */
    //     $direction = $request->query('direction', 'desc');
    //     /**
    //      * Đây là một tham số truy vấn để xác định số lượng mục trên mỗi trang.
    //      * @example 15
    //      * @default 15
    //      */
    //     $perPage = $request->query('per_page', 15);
    //     /**
    //      * Đây là một tham số truy vấn để xác định trang hiện tại.
    //      * @example 1
    //      * @default 1
    //      */
    //     $page = $request->query('page', 1);

    //     $pallets = Pallet::query()
    //         ->when($search, function ($query, $search) {
    //             return $query->where('name', 'like', "%{$search}%");
    //         })
    //         ->orderBy($sort, $direction)
    //         ->paginate($perPage, ['*'], 'page', $page);

    //     return PalletResource::collection($pallets);
    // }
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
     * Tạo mới một pallet cho kiện hàng.
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
        $validated = $request->validated();
        
        // Thêm các giá trị mặc định
        $data = array_merge($validated, [
            'checked_in_at' => now(),
            'checked_in_by' => Auth::id(),
            'status' => PalletStatus::IN_TRANSIT->value,
        ]);
        $crate = Crate::find($validated['crate_id']);
        $crate->status = CrateStatus::STORED->value;
        $crate->save();
        $pallet = Pallet::create($data);
        return new PalletResource($pallet);
    }

    /**
     * Cập nhập vị trí cho pallet.
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
        $validated = $request->validated();
        if($pallet->status === PalletStatus::STORED->value) {
            return new JsonResource([
                'message' => 'Không thể cập nhật pallet đã được lưu trữ.',
                'errors' => [
                    'status' => [
                        'Pallet đã được lưu trữ không thể cập nhật.'
                    ]
                ]
            ]);
        }
        if($pallet->status === PalletStatus::SHIPPED->value) {
            return new JsonResource([
                'message' => 'Không thể cập nhật pallet đã được giao hàng.',
                'errors' => [
                    'status' => [
                        'Pallet đã được giao hàng không thể cập nhật.'
                    ]
                ]
            ]);
        }

        

        $data = array_merge($validated, [
            'checked_in_at' => now(),
            'checked_in_by' => Auth::id(),
            'status' => PalletStatus::STORED->value,
        ]);
        $pallet->update($data);

        return new PalletResource($pallet);
    }

    // /**
    //  * Xóa một pallet.
    //  *
    //  * @param Pallet $pallet
    //  * @return JsonResponse
    //  *
    //  * @operationId deletePallet
    //  * @tags Pallets
    //  * @summary Xóa một pallet khỏi hệ thống
    //  *
    //  * @response 200 {
    //  *     "message": "Đã xóa pallet thành công"
    //  * }
    //  * @response 404 {
    //  *     "message": "Không tìm thấy pallet"
    //  * }
    //  */
    // public function destroy(Pallet $pallet): JsonResponse
    // {
    //     $pallet->delete();
    //     return response()->json(['message' => 'Pallet deleted successfully']);
    // }

}