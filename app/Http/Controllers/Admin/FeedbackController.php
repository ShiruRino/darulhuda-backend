<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    // Menampilkan daftar Kritik & Saran
    public function index(Request $request)
    {
        // Ambil relasi user (wali santri) agar nama pengirim bisa ditampilkan
        $query = Feedback::with('user');

        // Fitur Pencarian (Berdasarkan isi pesan atau nama pengirim)
        $query->when($request->search, function ($q, $search) {
            $q->where('message', 'like', "%{$search}%")
              ->orWhereHas('user', function ($userQuery) use ($search) {
                  $userQuery->where('name', 'like', "%{$search}%");
              });
        });

        // Filter status dibaca/belum dibaca
        $query->when($request->has('is_read') && $request->is_read != '', function ($q) use ($request) {
            $q->where('is_read', $request->is_read);
        });

        // Urutkan selalu dari yang terbaru masuk
        $feedbacks = $query->orderBy('created_at', 'desc')
                           ->paginate(15)
                           ->appends($request->query());

        return view('admin.feedbacks.index', compact('feedbacks'));
    }

    // Aksi untuk menandai pesan sudah dibaca
    public function markAsRead($id)
    {
        $feedback = Feedback::findOrFail($id);
        
        // Ubah status menjadi true (sudah dibaca)
        $feedback->update([
            'is_read' => true
        ]);

        return back()->with('success', 'Pesan telah ditandai sebagai sudah dibaca.');
    }

    // Aksi untuk menghapus pesan
    public function destroy($id)
    {
        Feedback::findOrFail($id)->delete();
        
        return back()->with('success', 'Kritik & saran berhasil dihapus.');
    }
}