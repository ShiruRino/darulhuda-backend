<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

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
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'attachment_url' => 'nullable|url' // Opsional jika ada link lampiran
        ]);

        Announcement::create($request->all());

        return back()->with('success', 'Pengumuman berhasil diterbitkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'attachment_url' => 'nullable|url'
        ]);

        $announcement = Announcement::findOrFail($id);
        $announcement->update($request->all());

        return back()->with('success', 'Pengumuman berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Announcement::findOrFail($id)->delete();
        return back()->with('success', 'Pengumuman berhasil dihapus!');
    }
}