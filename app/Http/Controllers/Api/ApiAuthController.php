<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiAuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Input tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cari user berdasarkan username
        $user = User::where('username', $request->username)->first();

        // Validasi password (menggunakan kolom password_hash)
        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Username atau password salah'
            ], 401);
        }

        // Cek status keaktifan user
        if ($user->status !== 'Aktif') {
            return response()->json([
                'status' => 'error',
                'message' => 'Akun Anda tidak aktif'
            ], 403);
        }

        // Update last login
        $user->update(['last_login' => now()]);

        // Buat access token menggunakan Laravel Sanctum
        $token = $user->createToken('android_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [
                'id_user' => $user->id_user,
                'username' => $user->username,
                'nama_lengkap' => $user->nama_lengkap,
                'role' => $user->role,
                'id_ruang' => $user->id_ruang
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil'
        ], 200);
    }
}
