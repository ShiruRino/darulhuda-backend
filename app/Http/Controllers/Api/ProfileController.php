<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    // 1. Mengambil data profil user yang sedang login
    public function show(Request $request)
    {
        // Ambil data user beserta data anak-anaknya
        $user = $request->user()->load('students');

        return response()->json([
            'status' => 'success',
            'message' => 'Data profil berhasil diambil',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'nik' => $user->nik,
                'relationship' => $user->relationship,
                'is_active' => (bool) $user->is_active,
                'joined_at' => $user->created_at->translatedFormat('d M Y'),
                'jumlah_anak' => $user->students->count(),
            ]
        ]);
    }

    // 2. Mengupdate profil dasar (Nama, Email, No. HP)
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users')->ignore($user->id), // Abaikan nomor yang sama jika milik dia sendiri
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ], [
            'phone_number.unique' => 'Nomor telepon ini sudah digunakan oleh akun lain.',
            'email.unique' => 'Email ini sudah terdaftar di akun lain.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui',
            'data' => $user
        ]);
    }

    // 3. Mengupdate Password
    public function updatePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password', // Mengecek apakah password lama benar
            'password' => 'required|string|min:8|confirmed',   // Harus cocok dengan password_confirmation
        ], [
            'current_password.current_password' => 'Password saat ini salah.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil diubah. Silakan gunakan password baru pada login berikutnya.'
        ]);
    }
}