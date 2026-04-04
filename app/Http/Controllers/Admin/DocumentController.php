<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
// Asumsi model tagihanmu bernama Invoice atau Payment, sesuaikan jika berbeda
use App\Models\Invoice; 
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentController extends Controller
{
    // 1. Generate Kartu Santri (ID Card)
    public function generateStudentCard($id)
    {
        $student = Student::findOrFail($id);

        // Load view khusus PDF dan atur ukuran kertas ke A6 (cocok untuk ID Card)
        $pdf = Pdf::loadView('pdf.student_card', compact('student'))
                  ->setPaper('a6', 'portrait');

        // Gunakan stream() untuk melihat di browser, atau download() untuk langsung unduh
        return $pdf->stream('Kartu_Santri_' . $student->nisn . '.pdf');
    }

    // 2. Generate Bukti Pembayaran
    public function generateInvoice($id)
    {
        // Panggil tagihan beserta data santrinya
        $invoice = Invoice::with('student')->findOrFail($id);

        // Load view PDF untuk kertas A4 atau Letter
        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('Kuitansi_' . $invoice->id . '_' . $invoice->student->name . '.pdf');
    }
}