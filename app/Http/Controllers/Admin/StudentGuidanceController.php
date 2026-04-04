<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentGuidance;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentGuidanceController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentGuidance::with('student');

        // 1. FILTER: Pencarian (Judul Catatan atau Nama Santri)
        $query->when($request->search, function ($q, $search) {
            return $q->where('title', 'like', "%{$search}%")
                     ->orWhereHas('student', function ($sq) use ($search) {
                         $sq->where('name', 'like', "%{$search}%");
                     });
        });

        // 2. FILTER: Jenis Pembinaan (Prestasi/Pelanggaran/Bimbingan)
        $query->when($request->type, function ($q, $type) {
            return $q->where('type', $type);
        });

        // 3. SORTING: Berdasarkan tanggal kejadian (Default: Terbaru)
        $sortDir = $request->get('sort_dir', 'desc');
        $query->orderBy('date', $sortDir)->orderBy('created_at', $sortDir);

        $guidances = $query->paginate(15)->appends($request->query());
        
        // Ambil data santri untuk dropdown di Modal Tambah Data
        $students = Student::orderBy('name')->get();

        return view('admin.guidances.index', compact('guidances', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'  => 'required|exists:students,id',
            'type'        => 'required|in:achievement,violation,guidance',
            'date'        => 'required|date',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'points'      => 'required|integer',
            'handled_by'  => 'nullable|string|max:100',
        ]);

        StudentGuidance::create($validated);

        return back()->with('success', 'Catatan pembinaan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'type'        => 'required|in:achievement,violation,guidance',
            'date'        => 'required|date',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'points'      => 'required|integer',
            'handled_by'  => 'nullable|string|max:100',
        ]);

        $guidance = StudentGuidance::findOrFail($id);
        $guidance->update($validated);

        return back()->with('success', 'Catatan pembinaan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        StudentGuidance::findOrFail($id)->delete();
        return back()->with('success', 'Catatan pembinaan berhasil dihapus.');
    }
}