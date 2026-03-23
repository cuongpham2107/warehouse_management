<?php

namespace App\Http\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Đăng nhập và tạo token cho người dùng
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function loginWithEmployeeCode(LoginRequest $request):JsonResponse
    {
        //Đăng nhập bằng mã nhân viên dựa theo api  này https://id.asgl.net.vn/api/internal/users/by-asgl-id/{{$employee_code}} header x-api-key: 76d43e23a183b85d31f140acca740976
        $validated = $request->validated();
        $employeeCode = $validated['employee_code'];

        $response = Http::withHeaders([
            'x-api-key' => '76d43e23a183b85d31f140acca740976',
        ])->get("https://id.asgl.net.vn/api/internal/users/by-asgl-id/{$employeeCode}");
       
        if ($response->successful()) {
            $userData = $response->json();
            
            // Tìm user theo asgl_id hoặc email
            $user = User::where('asgl_id', $userData['data']['user']['username'])
                       ->orWhere('email', $userData['data']['user']['email'])
                       ->first();
            
            if (!$user) {
                // Tạo user mới với password mặc định
                $user = User::create([
                    'name' => $userData['data']['user']['full_name'],
                    'asgl_id' => $userData['data']['user']['username'],
                    'email' => $userData['data']['user']['email'],
                    'password' => bcrypt('asgl_user_' . $userData['data']['user']['username']),
                ]);
            } else {
                // Cập nhật thông tin user nếu đã tồn tại
                $user->update([
                    'name' => $userData['data']['user']['full_name'],
                    'email' => $userData['data']['user']['email'],
                ]);
            }
            
            Auth::login($user);

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                "success" => true,
                "message" => "",
                "error_code" => null,
                'data' => [
                    'employees' => new UserResource($user),
                    'token' => $token
                ]
            ]);
        }

        return response()->json([
            'message' => 'Đăng nhập không thành công'
        ], 401);
    }

    public function checkEmployeeCode($asgl_id): JsonResponse
    {
        // Kiểm tra xem mã nhân viên có tồn tại trong hệ thống hay không
        $response = Http::withHeaders([
            'x-api-key' => '76d43e23a183b85d31f140acca740976',
        ])->get("https://id.asgl.net.vn/api/internal/users/by-asgl-id/{$asgl_id}");

        if ($response->successful()) {
            $userData = $response->json();
            return response()->json([
                "success" => true,
                "message" => "",
                "error_code" => null,
                'data' => [
                    'id' => $userData['data']['user']['id'],
                    'asgl_id' => $userData['data']['user']['asgl_id'],
                    'name' => $userData['data']['user']['full_name'],
                ]
            ]);
        }
        return response()->json([
            'exists' => false,
            'message' => 'Mã nhân viên không tồn tại'
        ], 404);
    }
    /**
     * Đăng xuất và thu hồi token
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::user()->tokens()->delete();

        return response()->json([
            'message' => 'Đăng xuất thành công'
        ]);
    }
}
