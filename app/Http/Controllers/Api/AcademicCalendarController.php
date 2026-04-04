<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicCalendar;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AcademicCalendarController extends Controller
{
    public function index(Request $request)
    {
        $query = AcademicCalendar::query();

        // 1. FILTER: Pencarian (Judul atau Deskripsi)
        $query->when($request->query('search'), function ($q, $search) {
            return $q->where(function ($subQuery) use ($search) {
                $subQuery->where('title', 'like', "%{$search}%")
                         ->orWhere('description', 'like', "%{$search}%");
            });
        });

        // 2. FILTER: Kategori (holiday, exam, event, other)
        $query->when($request->query('type'), function ($q, $type) {
            return $q->where('type', $type);
        });

        // 3. FILTER: Rentang Tanggal (Mencari acara yang terjadi di antara tanggal tertentu)
        $query->when($request->query('from_date'), function ($q, $from) {
            return $q->whereDate('start_date', '>=', $from);
        });
        
        $query->when($request->query('to_date'), function ($q, $to) {
            return $q->where(function ($sub) use ($to) {
                // Mengecek apakah start_date ATAU end_date masih di bawah to_date
                $sub->whereDate('start_date', '<=', $to)
                    ->orWhereDate('end_date', '<=', $to);
            });
        });

        // 4. SORTING: Default diurutkan dari tanggal acara terdekat (Ascending)
        $sortBy = $request->query('sort_by', 'start_date');
        $sortDir = $request->query('sort_dir', 'asc');
        
        $allowedSorts = ['title', 'start_date', 'type', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'desc' ? 'desc' : 'asc');
        }

        // Eksekusi Paginasi
        $events = $query->paginate(15)->appends($request->query());

        // Transformasi Data agar rapi dibaca oleh Flutter
        $data = $events->getCollection()->map(function ($item) {
            $startDateObj = Carbon::parse($item->start_date);
            $endDateObj = $item->end_date ? Carbon::parse($item->end_date) : null;
            
            return [
                'id' => $item->id,
                'judul' => $item->title,
                'deskripsi' => $item->description ?? '-',
                'kategori_raw' => $item->type,
                'kategori_label' => ucfirst($item->type),
                'tanggal_mulai_raw' => $item->start_date,
                'tanggal_selesai_raw' => $item->end_date,
                'tanggal_mulai_formatted' => $startDateObj->translatedFormat('d F Y'),
                'tanggal_selesai_formatted' => $endDateObj ? $endDateObj->translatedFormat('d F Y') : null,
                'rentang_waktu' => $endDateObj && ($item->start_date != $item->end_date)
                                   ? $startDateObj->format('d') . ' - ' . $endDateObj->translatedFormat('d F Y')
                                   : $startDateObj->translatedFormat('d F Y'),
                'is_satu_hari' => is_null($item->end_date) || ($item->start_date == $item->end_date),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'meta' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'total' => $events->total(),
            ]
        ]);
    }
}