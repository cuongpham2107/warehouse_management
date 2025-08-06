<?php

namespace App\Http\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Đăng nhập và lấy token.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     *
     * @operationId login
     * @tags Authentication
     * @summary Đăng nhập vào hệ thống
     *
     * @requestBody {
     *     "required": true,
     *     "content": {
     *         "application/json": {
     *             "schema": {
     *                 "type": "object",
     *                 "required": ["email", "password"],
     *                 "properties": {
     *                     "email": {
     *                         "type": "string",
     *                         "format": "email",
     *                         "description": "Email đăng nhập",
     *                         "example": "admin@asgl.com"
     *                     },
     *                     "password": {
     *                         "type": "string",
     *                         "format": "password",
     *                         "description": "Mật khẩu",
     *                         "example": "Admin@123"
     *                     }
     *                 }
     *             },
     *             "example": {
     *                 "email": "admin@asgl.com",
     *                 "password": "Admin@123"
     *             }
     *         }
     *     }
     * }
     *
     * @response 200 {
     *     "data": {
     *         "user": {
     *             "id": 1,
     *             "name": "John Doe",
     *             "email": "john@example.com",
     *             "created_at": "2024-07-28T10:00:00Z"
     *         },
     *         "token": "1|abcdef123456..."
     *     }
     * }
     * @response 401 {
     *     "message": "Thông tin đăng nhập không chính xác"
     * }
     * @response 422 {
     *     "message": "Dữ liệu không hợp lệ",
     *     "errors": {
     *         "email": ["Email không được để trống"],
     *         "password": ["Mật khẩu không được để trống"]
     *     }
     * }
     */
    
    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->validated())) {
            return response()->json([
                'message' => 'Thông tin đăng nhập không chính xác'
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'data' => [
                'user' => new UserResource($user),
                'token' => $token
            ]
        ]);
    }
    public function loginWithEmployeeCode(Request $request):JsonResponse
    {
        $user = User::where('name', $request->input('employee_code'))
            ->first();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'data' => [
                'user' => new UserResource($user),
                'token' => $token
            ]
        ]);
    }
    /**
     * Đăng xuất và thu hồi token.
     *
     * @return JsonResponse
     *
     * @operationId logout
     * @tags Authentication
     * @summary Đăng xuất khỏi hệ thống
     *
     * @response 200 {
     *     "message": "Đăng xuất thành công"
     * }
     */
    public function logout(): JsonResponse
    {
        Auth::user()->tokens()->delete();

        return response()->json([
            'message' => 'Đăng xuất thành công'
        ]);
    }
}
