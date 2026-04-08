<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\UserStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $plainPassword = Str::password(20);

        $userStatusId = UserStatus::query()
            ->where('code', UserStatus::CODE_QUICK_REGISTRATION)
            ->firstOrFail()
            ->id;

        $user = User::create([
            'email' => $request->email,
            'password' => $plainPassword,
            'identity_type' => $request->identity_type,
            'identity_value' => $request->identity_value,
            'phone' => $request->phone,
            'role_id' => Role::query()->where('name', 'aspirante')->firstOrFail()->id,
            'user_status_id' => $userStatusId,
            'folio' => null,
            'folio_type' => null,
        ]);

        $user->load('userStatus');

        /*
         * Solo APP_ENV=local: escribe credenciales en storage/logs para probar login tras el registro.
         * No activar en staging/producción (no sustituye el correo de confirmación).
         */
        if (app()->isLocal()) {
            Log::info('Registro rápido: credenciales para pruebas locales', [
                'email' => $user->email,
                'identity_type' => $user->identity_type,
                'identity_value' => $user->identity_value,
                'phone' => $user->phone,
                'password' => $plainPassword,
            ]);
        }

        // TODO: enviar correo de confirmación con la contraseña en entornos no locales.

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'folio' => $user->folio,
                'user_status' => [
                    'id' => $user->user_status_id,
                    'code' => $user->userStatus?->code,
                ],
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::with('userStatus')->where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'folio' => $user->folio,
                'email' => $user->email,
                'user_status' => [
                    'id' => $user->user_status_id,
                    'code' => $user->userStatus?->code,
                ],
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout exitoso',
        ]);
    }

    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout de todos los dispositivos exitoso',
        ]);
    }
}
