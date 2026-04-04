<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        // Panggil data feedback beserta data orang tua (user) yang mengirim
        $query = Feedback::with('user');

        // 1. FILTER: Pencarian berdasarkan Nama Pengirim atau Isi Pesan
        $query->when($request->search, function ($q, $search) {
            return $q->where(function ($sub) use ($search) {
                $sub->where('message', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        });

        // 2. FILTER: Tingkat Kepuasan (satisfied / not_satisfied)
        $query->when($request->satisfaction, function ($q, $satisfaction) {
            return $q->where('satisfaction', $satisfaction);
        });

        // 3. FILTER: Status Dibaca (0 = Belum, 1 = Sudah)
        if ($request->has('is_read') && $request->is_read !== null) {
            $query->where('is_read', $request->is_read);
        }

        // 4. SORTING: Berdasarkan tanggal (Default: Terbaru / desc)
        $sortDir = $request->get('sort_dir', 'desc');
        $query->orderBy('created_at', $sortDir);

        // Paginasi
        $feedbacks = $query->paginate(15)->appends($request->query());

        // View akan kita buat belakangan
        return view('admin.feedbacks.index', compact('feedbacks'));
    }

    // Fungsi untuk menandai bahwa pesan sudah dibaca
    public function markAsRead($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->update(['is_read' => true]);

        return back()->with('success', 'Masukan telah ditandai sebagai sudah dibaca.');
    }

    // Fungsi untuk menghapus masukan (Opsional, jika admin ingin membersihkan data lama)
    public function destroy($id)
    {
        Feedback::findOrFail($id)->delete();
        return back()->with('success', 'Kritik & saran berhasil dihapus.');
    }
}