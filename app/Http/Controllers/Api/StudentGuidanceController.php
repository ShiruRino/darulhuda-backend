<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudentGuidance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StudentGuidanceController extends Controller
{
    public function index(Request $request, $student_id)
    {
        $query = StudentGuidance::where('student_id', $student_id);

        // FILTER: Berdasarkan Jenis (achievement, violation, guidance)
        $query->when($request->query('type'), function ($q, $type) {
            return $q->where('type', $type);
        });

        // Urutkan berdasarkan tanggal kejadian terbaru
        $guidances = $query->orderBy('date', 'desc')
                           ->orderBy('created_at', 'desc')
                           ->paginate(15)
                           ->appends($request->query());

        // Hitung akumulasi poin santri (total keseluruhan dari awal masuk)
        $totalPoints = StudentGuidance::where('student_id', $student_id)->sum('points');

        // Transformasi data agar mudah dikonsumsi oleh Flutter
        $data = $guidances->getCollection()->map(function ($item) {
            return [
                'id' => $item->id,
                'tipe_raw' => $item->type,
                'tipe_label' => $this->getTypeLabel($item->type),
                'tanggal_raw' => $item->date,
                'tanggal_formatted' => Carbon::parse($item->date)->translatedFormat('d F Y'),
                'judul' => $item->title,
                'deskripsi' => $item->description,
                'poin' => (int) $item->points,
                'ditangani_oleh' => $item->handled_by ?? '-',
            ];
        });

        return response()->json([
            'status' => 'success',
            'summary' => [
                'total_poin_saat_ini' => $totalPoints // Sangat berguna untuk ditampilkan besar-besar di UI aplikasi HP
            ],
            'data' => $data,
            'meta' => [
                'current_page' => $guidances->currentPage(),
                'last_page' => $guidances->lastPage(),
                'total' => $guidances->total(),
            ]
        ]);
    }

    // Fungsi bantuan untuk mengubah tipe bahasa Inggris menjadi label Indonesia
    private function getTypeLabel($type)
    {
        $labels = [
            'achievement' => 'Prestasi',
            'violation' => 'Pelanggaran',
            'guidance' => 'Bimbingan & Konseling'
        ];
        return $labels[$type] ?? 'Lainnya';
    }
}