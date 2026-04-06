<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\FolioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, FolioService $folioService)
    {
        $folio = $folioService->assign($request->folio_type);

        $user = User::create([
            'folio' => $folio,
            'email' => $request->email,
            'password' => $request->password, // 🔥 auto-hash por casts
            'identity_type' => $request->identity_type,
            'identity_value' => $request->identity_value,
            'phone' => $request->phone,
            'role_id' => 3,
            'folio_type' => $request->folio_type
        ]);

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'folio' => $user->folio
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'folio' => 'required|string|size:9',
            'password' => 'required|string'
        ]);

        $user = User::where('folio', $request->folio)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'folio' => $user->folio,
                'email' => $user->email
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout exitoso'
        ]);
    }

    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout de todos los dispositivos exitoso'
        ]);
    }
}