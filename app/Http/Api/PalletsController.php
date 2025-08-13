<?php

namespace App\Http\Api;

use App\Enums\PalletStatus;
use App\Enums\CrateStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePalletRequest;
use App\Http\Resources\PalletResource;
use App\Models\Pallet;
use App\Models\Crate;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PalletsController extends Controller
{
    /**
     * Hiển thị danh sách các pallet đang di chuyển và chờ gắn vị trí
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResource
     *
     */
    public function index(\Illuminate\Http\Request $request): JsonResource
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

        $pallets = Pallet::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->where('status', PalletStatus::IN_TRANSIT->value) // Lọc các pallet không phải đã lưu trữ
            ->orderBy($sort, $direction)
            ->paginate($perPage, ['*'], 'page', $page);

        return PalletResource::collection($pallets);
    }
    /**
     * Hiển thị thông tin chi tiết của một pallet
     *
     * @param Pallet $pallet ID của Pallet
     * @return JsonResource
     */
    public function show(Pallet $pallet): JsonResource
    {
        return new PalletResource($pallet);
    }

    /**
     * Tìm kiếm pallet theo pallet_id
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResource
     */
    public function searchByPalletId(\Illuminate\Http\Request $request): JsonResource
    {
        $palletId = $request->query('pallet_id');

        if (!$palletId) {
            return new JsonResource([
                'message' => 'Validation failed',
                'errors' => [
                    'pallet_id' => ['pallet_id là bắt buộc.']
                ]
            ]);
        }

        $pallet = Pallet::where('pallet_id', $palletId)->first();

        if (!$pallet) {
            return new JsonResource([
                'message' => 'Pallet not found',
                'errors' => [
                    'pallet_id' => ['Pallet không tồn tại.']
                ]
            ]);
        }

        return new PalletResource($pallet);
    }

    /**
     * 3. Tạo mới một pallet cho kiện hàng
     *
     * @param StorePalletRequest $request
     * @return JsonResource
     */
    public function store(StorePalletRequest $request): JsonResource
    {
        $validated = $request->validated();
        // Kiểm tra trường crate_code có tồn tại trong validated
        if (!isset($validated['crate_code'])) {
            return new JsonResource([
                'message' => 'crate_code là bắt buộc.',
                'errors' => [
                    'crate_code' => ['crate_code không được bỏ trống.']
                ]
            ]);
        }

        // Kiểm tra crate có tồn tại
        $crate = Crate::where('crate_id', $validated['crate_code'])->first();
        if (!$crate) {
            return new JsonResource([
                'message' => 'Crate not found.',
                'errors' => [
                    'crate_code' => ['Crate không tồn tại.']
                ]
            ]);
        }

        // Kiểm tra crate đã được gán pallet chưa
        if ($crate->pallet) {
            return new JsonResource([
                'message' => 'Crate đã được gán cho pallet khác.',
                'errors' => [
                    'crate_code' => ['Crate đã được gán pallet.']
                ]
            ]);
        }

        // Kiểm tra pallet_id trùng lặp
        if (isset($validated['pallet_code']) && Pallet::where('pallet_id', $validated['pallet_code'])->exists()) {
            return new JsonResource([
                'message' => 'Pallet ID đã tồn tại.',
                'errors' => [
                    'pallet_code' => ['Pallet ID đã tồn tại.']
                ]
            ]);
        }

        // Cập nhật trạng thái crate
        $crate->status = CrateStatus::STORED->value;
        $crate->save();

        $data = array_merge($validated, [
            'pallet_id' => $validated['pallet_code'],
            'crate_id' => $crate->id,
            'status' => PalletStatus::IN_TRANSIT->value,
        ]);
        $pallet = Pallet::create($data);

        // Ghi nhận activity cho pallet
        $pallet->activities()->create([
            'action' => 'attach_crate',
            'description' => 'Gắn crate "' . $crate->crate_id . '" vào pallet "' . $pallet->pallet_id . '"',
            'user_id' => Auth::id(),
            'action_time' => now(),
        ]);

        /** @status 200 */
        return new PalletResource($pallet);
    }

    /**
     * 4. Cập nhật vị trí cho pallet - hỗ trợ cả nhập kho và di chuyển vị trí
     *
     * @param \Illuminate\Http\Request $request
     * @param Pallet $pallet 
     * @return PalletResource
     */
    public function update(\Illuminate\Http\Request $request, Pallet $pallet): JsonResource
    {
        $request->validate([
            /**
             * Mã vị trí kho mới
             * @example WH-001
             */
            'location_code' => 'required|string|max:255',
            /**
             * Loại hành động
             * @example import
             */
            'action_type' => 'required|string|in:import,relocate'
        ]);

        $actionType = $request->action_type;
        $oldLocation = $pallet->location_code;
        $newLocation = $request->location_code;

        // Kiểm tra điều kiện dựa trên loại hành động
        if ($actionType === 'import') {
            // Nhập kho: kiểm tra pallet có thể nhập kho không
            if ($pallet->status === PalletStatus::STORED->value) {
                return new JsonResource([
                    'message' => 'Không thể nhập kho pallet đã được lưu trữ.',
                    'errors' => [
                        'status' => [
                            'Pallet đã được lưu trữ không thể nhập kho lại.'
                        ]
                    ]
                ]);
            }
            if ($pallet->status === PalletStatus::SHIPPED->value) {
                return new JsonResource([
                    'message' => 'Không thể nhập kho pallet đã được giao hàng.',
                    'errors' => [
                        'status' => [
                            'Pallet đã được giao hàng không thể nhập kho.'
                        ]
                    ]
                ]);
            }

            // Cập nhật cho nhập kho
            $pallet->update([
                'location_code' => $newLocation,
                'status' => PalletStatus::STORED->value,
                'updated_at' => now(),
            ]);

            // Ghi nhận activity cho nhập kho
            $pallet->activities()->create([
                'action' => 'import_pallet',
                'description' => 'Nhập kho pallet ' . $pallet->pallet_id . ' vào vị trí ' . $newLocation,
                'user_id' => Auth::id(),
                'action_time' => now(),
            ]);

        } else { // relocate
            // Di chuyển vị trí: kiểm tra pallet có thể di chuyển không
            if ($pallet->status === PalletStatus::SHIPPED->value) {
                return new JsonResource([
                    'message' => 'Không thể di chuyển pallet đã được giao hàng.',
                    'errors' => [
                        'status' => [
                            'Pallet đã được giao hàng không thể di chuyển.'
                        ]
                    ]
                ]);
            }

            // Cập nhật vị trí mới (giữ nguyên status)
            $pallet->update([
                'location_code' => $newLocation,
                'updated_at' => now(),
            ]);

            // Ghi nhận activity cho di chuyển
            $pallet->activities()->create([
                'action' => 'relocate_pallet',
                'description' => 'Chuyển pallet ' . $pallet->pallet_id . ' từ vị trí ' . ($oldLocation ?: 'chưa xác định') . ' sang vị trí ' . $newLocation,
                'user_id' => Auth::id(),
                'action_time' => now(),
            ]);
        }

        return new PalletResource($pallet);
    }
}