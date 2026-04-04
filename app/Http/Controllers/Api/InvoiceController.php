<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    // Mengambil daftar tagihan berdasarkan ID anak
    public function index(Request $request, $student_id)
    {
        // Mulai query dasar (hanya tagihan milik student_id tersebut)
        $query = Invoice::where('student_id', $student_id);

        // 1. FILTER: Berdasarkan status (contoh: ?status=unpaid)
        $query->when($request->query('status'), function ($q, $status) {
            return $q->where('status', $status);
        });

        // 2. SEARCH: Berdasarkan judul tagihan (contoh: ?search=SPP)
        $query->when($request->query('search'), function ($q, $search) {
            return $q->where('title', 'like', '%' . $search . '%');
        });

        // 3. SORTING: Mengatur kolom urutan dan arah urutan (ASC/DESC)
        // Default: urutkan berdasarkan 'created_at' secara 'desc' (terbaru)
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');
        
        // Pastikan kolom yang di-sort aman/valid agar tidak terkena SQL Injection
        $allowedSorts = ['created_at', 'due_date', 'amount', 'title'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        // Eksekusi query dengan paginasi
        $invoices = $query->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $invoices
        ]);
    }

    // Generate link WhatsApp untuk pembayaran
    public function checkout(Request $request, $invoice_id)
    {
        // Cari tagihan beserta relasi anaknya
        $invoice = Invoice::with('student')->find($invoice_id);

        // Validasi akses (pastikan tagihan ini milik anak dari user yang login)
        if (!$invoice || $invoice->student->user_id !== $request->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tagihan tidak ditemukan atau akses ditolak'
            ], 404);
        }

        if ($invoice->status === 'paid') {
            return response()->json([
                'status' => 'error',
                'message' => 'Tagihan ini sudah lunas'
            ], 400);
        }

        // --- KONFIGURASI WHATSAPP ---
        // Ganti dengan nomor WhatsApp Admin Keuangan (gunakan kode negara 62)
        $admin_wa_number = "6281234567890"; 
        
        $nominal = 'Rp ' . number_format($invoice->amount, 0, ',', '.');
        $nama_anak = $invoice->student->name;
        $kelas = $invoice->student->grade;

        // Template pesan yang akan otomatis terisi di WA
        $message = "Assalamu'alaikum Admin,\n\n";
        $message .= "Saya ingin konfirmasi pembayaran tagihan madrasah.\n";
        $message .= "Nama Santri: *$nama_anak*\n";
        $message .= "Kelas: *$kelas*\n";
        $message .= "Pembayaran: *$invoice->title*\n";
        $message .= "Nominal: *$nominal*\n\n";
        $message .= "Berikut saya lampirkan bukti transfernya. Terima kasih.";

        // Encode pesan agar aman ditaruh di URL
        $encoded_message = urlencode($message);
        $wa_link = "https://wa.me/{$admin_wa_number}?text={$encoded_message}";

        // Update status menjadi pending verification (Opsional: bisa ditaruh saat admin menyetujui, tapi untuk tracking ini bagus)
        $invoice->update(['status' => 'pending_verification']);

        return response()->json([
            'status' => 'success',
            'message' => 'Link checkout berhasil dibuat',
            'data' => [
                'wa_link' => $wa_link,
                'invoice_id' => $invoice->id,
                'status_sekarang' => $invoice->status
            ]
        ]);
    }
}