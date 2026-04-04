<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    // Menampilkan halaman pengaturan akun
    public function index()
    {
        $admin = Auth::user();
        return view('admin.profile.index', compact('admin'));
    }

    // Mengupdate informasi dasar (Nama & Email)
    // Mengupdate informasi dasar (Nama, Email & WhatsApp)
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $admin */
        $admin = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($admin->id), 
            ],
            // Tambahkan validasi nomor telepon
            'phone_number' => 'required|string|max:20', 
        ]);

        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number, // Simpan nomor ke database
        ]);

        return back()->with('success', 'Informasi profil & nomor WhatsApp berhasil diperbarui.');
    }

    // Mengupdate kata sandi (Password)
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password', 
            'password' => 'required|string|min:8|confirmed', 
        ], [
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.'
        ]);

        /** @var \App\Models\User $admin */
        $admin = Auth::user();
        
        $admin->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success_password', 'Password berhasil diubah.');
    }
}