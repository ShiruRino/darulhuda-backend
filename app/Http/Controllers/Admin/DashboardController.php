<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Invoice;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // --- 1. DATA UNTUK 4 CARD ---
        $totalPutra = Student::where('gender', 'Laki-laki')->count();
        $totalPutri = Student::where('gender', 'Perempuan')->count();
        
        $pembayaranBulanIni = Invoice::where('status', 'paid')
                                     ->whereMonth('updated_at', Carbon::now()->month)
                                     ->whereYear('updated_at', Carbon::now()->year)
                                     ->sum('amount');
                                     
        $tagihanBelumBayar = Invoice::whereIn('status', ['unpaid', 'pending_verification'])
                                    ->sum('amount');

        // --- 2. DATA GRAFIK 1: Tren Santri (Line Chart) ---
        // Mengelompokkan total santri berdasarkan tahun masuk
        $santriPerTahun = Student::select('admission_year', DB::raw('count(*) as total'))
                                 ->groupBy('admission_year')
                                 ->orderBy('admission_year', 'asc')
                                 ->pluck('total', 'admission_year')
                                 ->toArray();
                                 
        $chart1Labels = array_keys($santriPerTahun);
        $chart1Data = array_values($santriPerTahun);

        // --- 3. DATA GRAFIK 2: Keuangan (Bar Chart) ---
        $totalPembayaranMasuk = Invoice::where('status', 'paid')->sum('amount');
        $chart2Data = [$totalPembayaranMasuk, $tagihanBelumBayar];

        // --- 4. DATA GRAFIK 3: Feedback (Pie Chart) ---
        // Asumsi "Belum Isi" = Total Orang Tua dikurangi jumlah feedback yang masuk
        $puas = Feedback::where('satisfaction', 'satisfied')->count();
        $tidakPuas = Feedback::where('satisfaction', 'not_satisfied')->count();
        $totalOrangTua = User::where('role', 'parent')->count();
        
        $belumIsi = $totalOrangTua - ($puas + $tidakPuas);
        if ($belumIsi < 0) $belumIsi = 0; // Fallback untuk mencegah nilai minus

        $chart3Data = [$puas, $tidakPuas, $belumIsi];

        return view('admin.dashboard', compact(
            'totalPutra', 'totalPutri', 'pembayaranBulanIni', 'tagihanBelumBayar',
            'chart1Labels', 'chart1Data', 'chart2Data', 'chart3Data'
        ));
    }
}