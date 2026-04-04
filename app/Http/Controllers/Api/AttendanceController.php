<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request, $student_id)
    {
        $request->validate([
            'from_date' => 'nullable|date_format:Y-m-d',
            'to_date' => 'nullable|date_format:Y-m-d',
            'status' => 'nullable|in:present,sick,leave,absent',
        ]);
        // Mulai query dasar untuk anak tertentu
        $query = Attendance::where('student_id', $student_id);

        // 1. FILTER: Berdasarkan tanggal mulai (contoh: ?from_date=2026-04-01)
        $query->when($request->query('from_date'), function ($q, $from) {
            return $q->whereDate('date', '>=', $from);
        });

        // 2. FILTER: Berdasarkan tanggal akhir (contoh: ?to_date=2026-04-30)
        $query->when($request->query('to_date'), function ($q, $to) {
            return $q->whereDate('date', '<=', $to);
        });

        // 3. FILTER: Berdasarkan status (hadir, sakit, izin, alpa)
        $query->when($request->query('status'), function ($q, $status) {
            return $q->where('status', $status);
        });

        // 4. SORTING: Mengatur urutan tanggal
        // Default: dari tanggal terbaru ke terlama
        $sortDir = $request->query('sort_dir', 'desc');
        $query->orderBy('date', $sortDir === 'asc' ? 'asc' : 'desc');

        // Eksekusi dengan paginasi
        $attendances = $query->paginate(15)->appends($request->query());

        // Transformasi response (bisa menggunakan AttendanceResource jika sudah dibuat)
        $data = $attendances->getCollection()->map(function($item) {
            return [
                'id' => $item->id,
                'tanggal_raw' => $item->date,
                'tanggal_formatted' => Carbon::parse($item->date)->translatedFormat('d F Y'),
                'hari' => Carbon::parse($item->date)->translatedFormat('l'),
                'status_raw' => $item->status,
                'status_label' => ucfirst($item->status),
                'keterangan' => $item->notes ?? '-',
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'meta' => [
                'current_page' => $attendances->currentPage(),
                'last_page' => $attendances->lastPage(),
                'total' => $attendances->total(),
            ]
        ]);
    }
}