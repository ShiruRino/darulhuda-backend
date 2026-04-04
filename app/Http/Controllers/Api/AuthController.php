<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:users,phone_number', // Sekarang WAJIB
            'email' => 'nullable|string|email|unique:users,email', // Sekarang OPSIONAL
            'nik' => 'required|digits:16|unique:users,nik',
            'relationship' => 'required|in:Ayah,Ibu,Wali',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'nik' => $request->nik,
            'relationship' => $request->relationship,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'access_token' => $token
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required', // Input tunggal untuk email atau nomor telepon
            'password' => 'required',
        ]);

        // Logika untuk menentukan apakah input adalah email atau nomor telepon
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';

        $user = User::where($loginType, $request->login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Kredensial salah.'], 401);
        }

        // Tambahkan pengecekan ini
        if (!$user->is_active) {
            return response()->json(['status' => 'error', 'message' => 'Akun Anda telah dinonaktifkan. Silakan hubungi admin.'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'access_token' => $token
            ]
        ]);
    }
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string'
        ]);

        $user = $request->user();
        $user->update([
            'fcm_token' => $request->fcm_token
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'FCM Token berhasil diperbarui.'
        ]);
    }
}