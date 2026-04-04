<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(Request $request, $student_id)
    {
        $query = Grade::where('student_id', $student_id);

        // 1. FILTER: Tahun Ajaran (Contoh: ?academic_year=2025/2026)
        $query->when($request->query('academic_year'), function ($q, $year) {
            return $q->where('academic_year', $year);
        });

        // 2. FILTER: Semester (Contoh: ?semester=Ganjil)
        $query->when($request->query('semester'), function ($q, $semester) {
            return $q->where('semester', $semester);
        });

        // 3. FILTER: Tipe Nilai (Contoh: ?type=Rapor)
        $query->when($request->query('type'), function ($q, $type) {
            return $q->where('type', $type);
        });

        // 4. SEARCH: Berdasarkan Nama Mata Pelajaran (Contoh: ?search=Fiqih)
        $query->when($request->query('search'), function ($q, $search) {
            return $q->where('subject', 'like', "%{$search}%");
        });

        // 5. SORTING: Mengatur kolom urutan dan arah (ASC/DESC)
        // Default: Urutkan berdasarkan tahun ajaran terbaru
        $sortBy = $request->query('sort_by', 'academic_year');
        $sortDir = $request->query('sort_dir', 'desc');

        $allowedSorts = ['academic_year', 'subject', 'score', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        // Jika sort_by adalah academic_year, tambahkan sorting semester agar urutannya logis
        if ($sortBy === 'academic_year') {
            $query->orderBy('semester', $sortDir === 'asc' ? 'asc' : 'desc');
        }

        // Eksekusi query dengan paginasi
        $grades = $query->paginate(15)->appends($request->query());

        // Transformasi response agar mudah dikonsumsi Flutter
        $data = $grades->getCollection()->map(function ($item) {
            return [
                'id' => $item->id,
                'mata_pelajaran' => $item->subject,
                'tahun_ajaran' => $item->academic_year,
                'semester' => $item->semester,
                'tipe_nilai' => $item->type,
                'skor' => (float) $item->score,
                'catatan' => $item->notes ?? '-',
                'tanggal_input' => $item->created_at->translatedFormat('d M Y'),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'meta' => [
                'current_page' => $grades->currentPage(),
                'last_page' => $grades->lastPage(),
                'total' => $grades->total(),
            ]
        ]);
    }
}