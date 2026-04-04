<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Student; // Pastikan model Student sudah di-import

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ==========================================
        // 1. SEEDING MASTER INVOICES
        // ==========================================
        $masterInvoices = [
            ['title' => 'SPP Bulanan',   'amount' => 500000,  'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Uang Buku',     'amount' => 1200000, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Uang Seragam',  'amount' => 1500000, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Uang Kegiatan', 'amount' => 800000,  'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Uang Gedung',   'amount' => 5000000, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('master_invoices')->insertOrIgnore($masterInvoices);

        // Ambil data referensi nominal
        $masterData = DB::table('master_invoices')->pluck('amount', 'title');

        // ==========================================
        // 2. SEEDING INVOICES PER STUDENT
        // ==========================================
        
        // Mengambil semua siswa yang memiliki id dan admission_year
        // Menggunakan Eloquent sesuai permintaan Anda
        $students = Student::select('id', 'admission_year')->get(); 

        $invoicesToInsert = [];

        foreach ($students as $student) {
            
            // Asumsi format 'admission_year' di database adalah angka tahun (contoh: 2025)
            // Jika formatnya sudah '2025/2026', Anda bisa menggunakan fungsi substr($student->admission_year, 0, 4)
            $startYear = (int) $student->admission_year; 
            $endYear   = $startYear + 1;
            $tahunPelajaran = $startYear . '/' . $endYear;
            
            // Mengatur struktur bulan dan memastikan tahun bergeser di bulan Januari
            $bulanSpp = [
                ['nama' => 'Juli',      'bulan' => 7,  'tahun' => $startYear],
                ['nama' => 'Agustus',   'bulan' => 8,  'tahun' => $startYear],
                ['nama' => 'September', 'bulan' => 9,  'tahun' => $startYear],
                ['nama' => 'Oktober',   'bulan' => 10, 'tahun' => $startYear],
                ['nama' => 'November',  'bulan' => 11, 'tahun' => $startYear],
                ['nama' => 'Desember',  'bulan' => 12, 'tahun' => $startYear],
                ['nama' => 'Januari',   'bulan' => 1,  'tahun' => $endYear], // Tahun berganti
                ['nama' => 'Februari',  'bulan' => 2,  'tahun' => $endYear],
                ['nama' => 'Maret',     'bulan' => 3,  'tahun' => $endYear],
                ['nama' => 'April',     'bulan' => 4,  'tahun' => $endYear],
                ['nama' => 'Mei',       'bulan' => 5,  'tahun' => $endYear],
                ['nama' => 'Juni',      'bulan' => 6,  'tahun' => $endYear],
            ];

            // A. Generate Tagihan SPP (12 Bulan)
            foreach ($bulanSpp as $periode) {
                // Jatuh tempo setiap tanggal 10 di bulan tersebut
                $dueDate = Carbon::create($periode['tahun'], $periode['bulan'], 10)->format('Y-m-d');

                $invoicesToInsert[] = [
                    'student_id' => $student->id,
                    'title'      => 'SPP ' . $periode['nama'] . ' ' . $periode['tahun'],
                    'amount'     => $masterData['SPP Bulanan'] ?? 500000,
                    'due_date'   => $dueDate,
                    'status'     => 'unpaid',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // B. Generate Tagihan Tahunan / Sekali Bayar
            $tagihanTahunan = [
                ['master_title' => 'Uang Buku',     'invoice_title' => 'Uang Buku'],
                ['master_title' => 'Uang Seragam',  'invoice_title' => 'Uang Seragam'],
                ['master_title' => 'Uang Kegiatan', 'invoice_title' => 'Uang Kegiatan'],
                ['master_title' => 'Uang Gedung',   'invoice_title' => 'Uang Gedung'],
            ];

            // Jatuh tempo tagihan tahunan biasanya di awal tahun ajaran (misal 15 Juli di tahun masuk)
            $dueDateTahunan = Carbon::create($startYear, 7, 15)->format('Y-m-d');

            foreach ($tagihanTahunan as $tagihan) {
                $invoicesToInsert[] = [
                    'student_id' => $student->id,
                    'title'      => $tagihan['invoice_title'] . ' ' . $tahunPelajaran,
                    'amount'     => $masterData[$tagihan['master_title']] ?? 0,
                    'due_date'   => $dueDateTahunan,
                    'status'     => 'unpaid',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // ==========================================
        // 3. BULK INSERT DENGAN CHUNKING
        // ==========================================
        // Membagi array menjadi beberapa bagian (misal: 1000 baris per query) 
        // agar tidak membebani memori / melampaui limit parameter SQL
        $chunks = array_chunk($invoicesToInsert, 1000);

        foreach ($chunks as $chunk) {
            DB::table('invoices')->insert($chunk);
        }
    }
}