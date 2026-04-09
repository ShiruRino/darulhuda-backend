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
        // 1. Validasi input menggunakan sistem rating (1-5)
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:10',
            'rating'  => 'required|integer|min:1|max:5', 
        ], [
            'message.required' => 'Kolom kritik dan saran tidak boleh kosong.',
            'message.min' => 'Kritik dan saran minimal 10 karakter.',
            'rating.required' => 'Rating bintang wajib diisi.',
            'rating.min' => 'Rating minimal 1 bintang.',
            'rating.max' => 'Rating maksimal 5 bintang.',
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
            'rating'  => $request->rating,
            'is_read' => false // Setel default belum dibaca oleh admin
        ]);

        // 3. Kembalikan respon sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Terima kasih, masukan dan rating Anda telah berhasil dikirim.',
            'data' => $feedback
        ], 201);
    }
}