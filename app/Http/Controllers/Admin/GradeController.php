<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $query = Grade::with('student');

        // Filter: Siswa (Search Nama/NISN)
        $query->when($request->search, function ($q, $search) {
            return $q->whereHas('student', function($sq) use ($search) {
                $sq->where('name', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%");
            });
        });

        // Filter: Tahun Ajaran & Semester
        $query->when($request->academic_year, fn($q, $year) => $q->where('academic_year', $year));
        $query->when($request->semester, fn($q, $sem) => $q->where('semester', $sem));
        $query->when($request->type, fn($q, $type) => $q->where('type', $type));

        $grades = $query->latest()->paginate(20)->appends($request->query());
        $students = Student::orderBy('name')->get(); // Untuk pilihan di modal input

        return view('admin.grades.index', compact('grades', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'    => 'required|exists:students,id',
            'subject'       => 'required|string|max:100',
            'academic_year' => 'required|string', // Regex bisa ditambahkan: 202x/202x
            'semester'      => 'required|in:Ganjil,Genap',
            'type'          => 'required|in:Tugas,UTS,UAS,Rapor',
            'score'         => 'required|numeric|between:0,100',
            'notes'         => 'nullable|string',
        ]);

        Grade::create($validated);
        return back()->with('success', 'Nilai berhasil diinput.');
    }

    public function destroy($id)
    {
        Grade::findOrFail($id)->delete();
        return back()->with('success', 'Nilai berhasil dihapus.');
    }
}