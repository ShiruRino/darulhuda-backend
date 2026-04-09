<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Wajib ditambahkan untuk mengelola penghapusan file

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::query();

        // --- FILTER & SEARCH ---
        // Cari berdasarkan Judul atau Isi Pengumuman
        $query->when($request->search, function ($q, $search) {
            return $q->where('title', 'like', "%{$search}%")
                     ->orWhere('content', 'like', "%{$search}%");
        });

        // --- SORTING ---
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc'); // Default: Terbaru
        
        $allowedSorts = ['title', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir);
        }

        // Paginasi 10 data per halaman dan bawa parameter query
        $announcements = $query->paginate(10)->appends($request->query());

        return view('admin.announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'attachment_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048', // Validasi file maks 2MB
        ]);

        $path = null;
        // Jika ada file yang diupload, simpan ke storage
        if ($request->hasFile('attachment_file')) {
            $path = $request->file('attachment_file')->store('announcements', 'public');
        }

        Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'attachment_url' => $path, // Simpan path filenya ke database
        ]);

        // Catatan: Jika kamu masih menggunakan fitur Push Notification (FCM), 
        // kamu bisa memanggil FcmService di sini seperti sebelumnya.

        return back()->with('success', 'Pengumuman berhasil diterbitkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'attachment_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048', 
        ]);

        $announcement = Announcement::findOrFail($id);

        // Cek apakah Admin mengupload file lampiran BARU
        if ($request->hasFile('attachment_file')) {
            // 1. Hapus file lama dari storage (jika sebelumnya sudah ada lampiran)
            if ($announcement->attachment_url) {
                Storage::disk('public')->delete($announcement->attachment_url);
            }

            // 2. Simpan file baru ke storage
            $path = $request->file('attachment_file')->store('announcements', 'public');
            
            // 3. Update path di memori model
            $announcement->attachment_url = $path;
        }

        // Update teks judul dan konten
        $announcement->title = $request->title;
        $announcement->content = $request->content;
        
        // Simpan perubahan ke database
        $announcement->save();

        return back()->with('success', 'Pengumuman berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);

        // Hapus file fisik dari folder storage SEBELUM menghapus data dari database
        if ($announcement->attachment_url) {
            Storage::disk('public')->delete($announcement->attachment_url);
        }

        // Hapus data dari database
        $announcement->delete();
        
        return back()->with('success', 'Pengumuman dan file lampirannya berhasil dihapus!');
    }
}