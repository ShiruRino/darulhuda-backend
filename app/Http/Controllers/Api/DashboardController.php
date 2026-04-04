<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Invoice; // Sesuaikan jika nama model tagihanmu berbeda
use App\Models\Announcement;
use App\Models\AcademicCalendar;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // 1. Dapatkan profil santri
        // Jika orang tua punya >1 anak, kita bisa menerima parameter ?student_id=X
        $studentId = $request->query('student_id');
        
        if ($studentId) {
            $student = Student::where('id', $studentId)
                              ->where('user_id', $user->id) // Pastikan hanya bisa akses anak sendiri
                              ->first();
        } else {
            // Default: Ambil anak pertama
            $student = Student::where('user_id', $user->id)->first();
        }

        if (!$student) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Data santri belum terhubung ke akun Anda. Silakan hubungi Admin.'
            ], 404);
        }

        // 2. Ringkasan Keuangan (Ambil semua tagihan yang belum lunas untuk anak ini)
        $unpaidInvoices = Invoice::where('student_id', $student->id)
            ->whereIn('status', ['unpaid', 'pending_verification'])
            ->orderBy('due_date', 'asc')
            ->get();

        $totalTagihan = $unpaidInvoices->sum('amount');
        $tagihanTerdekat = $unpaidInvoices->first(); // Mengambil yang paling mendesak

        // 3. Pengumuman Terbaru (Ambil 3 teratas)
        $announcements = Announcement::orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($a) {
                return [
                    'id' => $a->id,
                    'judul' => $a->title,
                    'tanggal' => $a->created_at->translatedFormat('d M Y'),
                    'isi_singkat' => \Illuminate\Support\Str::limit($a->content, 60, '...'),
                ];
            });

        // 4. Agenda / Kalender Terdekat (Ambil 3 agenda yang belum lewat)
        $upcomingEvents = AcademicCalendar::whereDate('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->take(3)
            ->get()
            ->map(function ($e) {
                return [
                    'id' => $e->id,
                    'judul' => $e->title,
                    'tanggal' => Carbon::parse($e->start_date)->translatedFormat('d M Y'),
                    'tipe' => $e->type // holiday, exam, event
                ];
            });

        // 5. Susun Response JSON
        return response()->json([
            'status' => 'success',
            'data' => [
                'profil_wali' => [
                    'nama' => $user->name,
                    'sapaan' => 'Halo, ' . strtok($user->name, " "), // Mengambil nama panggilan
                ],
                'profil_santri' => [
                    'id' => $student->id,
                    'nama' => $student->name,
                    'kelas' => $student->grade,
                    'asrama' => $student->dormitory ?? 'Belum ada asrama',
                    'foto_url' => $student->photo_url ? asset('storage/' . $student->photo_url) : null,
                ],
                'ringkasan_keuangan' => [
                    'total_tunggakan' => (int) $totalTagihan,
                    'jumlah_tunggakan_berjalan' => $unpaidInvoices->count(),
                    'tagihan_terdekat' => $tagihanTerdekat ? [
                        'judul' => $tagihanTerdekat->title,
                        'nominal' => (int) $tagihanTerdekat->amount,
                        'jatuh_tempo' => Carbon::parse($tagihanTerdekat->due_date)->translatedFormat('d M Y'),
                        'is_overdue' => Carbon::parse($tagihanTerdekat->due_date)->isPast()
                    ] : null
                ],
                'pengumuman_terbaru' => $announcements,
                'agenda_terdekat' => $upcomingEvents,
            ]
        ]);
    }
}