<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi input dari aplikasi mobile
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:10',
            // Ubah validasi rating menjadi satisfaction dengan enum
            'satisfaction' => 'required|in:satisfied,not_satisfied', 
        ], [
            'message.required' => 'Kolom kritik dan saran tidak boleh kosong.',
            'message.min' => 'Kritik dan saran minimal 10 karakter.',
            'satisfaction.required' => 'Pilihan tingkat kepuasan wajib diisi.',
            'satisfaction.in' => 'Pilihan kepuasan tidak valid (harus satisfied atau not_satisfied).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Simpan ke database, hubungkan dengan user yang sedang login
        $feedback = Feedback::create([
            'user_id' => $request->user()->id,
            'message' => $request->message,
            'satisfaction' => $request->satisfaction, // Menggunakan kolom yang baru
        ]);

        // 3. Kembalikan respon sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Terima kasih, masukan Anda telah berhasil dikirim.',
            'data' => $feedback
        ], 201);
    }
}