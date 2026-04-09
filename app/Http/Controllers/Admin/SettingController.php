<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // Ambil semua setting dan ubah menjadi array asosiatif (key => value) agar mudah dipanggil di Blade
        $settings = Setting::pluck('value', 'key')->toArray();
        
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'wa_payment_number' => 'required|string|max:20',
            // Nanti kamu bisa tambahkan validasi lain di sini jika ada setting baru (misal: nama_sekolah)
        ]);

        // Simpan atau update setting WA Pembayaran
        Setting::updateOrCreate(
            ['key' => 'wa_payment_number'],
            ['value' => $request->wa_payment_number]
        );

        return back()->with('success', 'Pengaturan sistem berhasil diperbarui!');
    }
}