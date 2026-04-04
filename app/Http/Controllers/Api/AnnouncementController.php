<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Http\Resources\AnnouncementResource;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::query();

        // SEARCH: Mencari di judul atau isi pengumuman
        $query->when($request->query('search'), function ($q, $search) {
            return $q->where('title', 'like', '%' . $search . '%')
                     ->orWhere('content', 'like', '%' . $search . '%');
        });

        // SORTING: Default terbaru ke terlama
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');
        
        $allowedSorts = ['created_at', 'title'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        $announcements = $query->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => AnnouncementResource::collection($announcements),
            'meta' => [
                'current_page' => $announcements->currentPage(),
                'last_page' => $announcements->lastPage(),
            ]
        ]);
    }
}