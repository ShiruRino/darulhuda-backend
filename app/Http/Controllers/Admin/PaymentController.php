<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\MasterInvoice;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        // --- DATA TAB 1: MASTER INVOICE ---
        $masterInvoices = MasterInvoice::all();

        // --- DATA TAB 2: INVOICE SANTRI (Dengan Search & Filter) ---
        $query = Invoice::with('student');

        // Filter Status
        $query->when($request->query('status'), function ($q, $status) {
            return $q->where('status', $status);
        });

        // Search (Cari berdasarkan Judul Tagihan atau Nama Anak/NISN)
        $query->when($request->query('search'), function ($q, $search) {
            return $q->where(function($subQuery) use ($search) {
                $subQuery->where('title', 'like', "%{$search}%")
                         ->orWhereHas('student', function($studentQuery) use ($search) {
                             $studentQuery->where('name', 'like', "%{$search}%")
                                          ->orWhere('nisn', 'like', "%{$search}%");
                         });
            });
        });

        // Sorting
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');
        $allowedSorts = ['created_at', 'due_date', 'amount', 'title', 'status'];
        
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir);
        }

        // Paginate dan appends query string agar paginasi tidak mereset filter
        $invoices = $query->paginate(15)->appends($request->query());

        return view('admin.payments.index', compact('masterInvoices', 'invoices'));
    }

    // --- FUNGSI CRUD MASTER INVOICE ---
    public function storeMaster(Request $request)
    {
        $request->validate(['title' => 'required', 'amount' => 'required|numeric']);
        MasterInvoice::create($request->all());
        return back()->with('success', 'Template tagihan berhasil ditambahkan.');
    }

    public function updateMaster(Request $request, $id)
    {
        $request->validate(['title' => 'required', 'amount' => 'required|numeric']);
        MasterInvoice::findOrFail($id)->update($request->all());
        return back()->with('success', 'Template tagihan berhasil diperbarui.');
    }

    public function destroyMaster($id)
    {
        MasterInvoice::findOrFail($id)->delete();
        return back()->with('success', 'Template tagihan dihapus.');
    }

    // --- FUNGSI UPDATE STATUS INVOICE SANTRI ---
    public function updateInvoiceStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:unpaid,pending_verification,paid']);
        $invoice = Invoice::findOrFail($id);
        $invoice->update(['status' => $request->status]);
        
        return back()->with('success', 'Status tagihan ' . $invoice->student->name . ' berhasil diubah menjadi ' . $request->status . '.');
    }
}