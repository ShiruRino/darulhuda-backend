<?php
namespace App\Observers;

use App\Models\Student;
use App\Models\MasterInvoice;
use App\Models\Invoice;
use Carbon\Carbon;

class StudentObserver
{
    /**
     * Trigger saat data Student BARU saja DITAMBAHKAN.
     */
    public function created(Student $student): void
    {
        // 1. Ambil semua template tagihan yang statusnya aktif
        $masterInvoices = MasterInvoice::where('is_active', true)->get();

        // 2. Buat tagihan untuk santri ini berdasarkan template tersebut
        foreach ($masterInvoices as $master) {
            Invoice::create([
                'student_id' => $student->id,
                'title' => $master->title,
                'amount' => $master->amount,
                'due_date' => Carbon::now()->addDays(30), // Jatuh tempo 30 hari dari sekarang
                'status' => 'unpaid',
            ]);
        }
    }

    /**
     * Trigger saat data Student DIHAPUS.
     */
    public function deleted(Student $student): void
    {
        // Hapus semua tagihan yang dimiliki oleh santri ini agar tidak menjadi data sampah (orphan data)
        // $student->invoices()->delete();
        
        // Catatan: Jika di migration 'invoices' kamu sudah memakai 
        // $table->foreignId('student_id')->constrained()->cascadeOnDelete();
        // baris kode di atas sebenarnya opsional karena database SQL sudah menghapusnya otomatis. 
        // Tapi tetap aman ditaruh di sini sebagai pengaman lapis dua.
    }
}