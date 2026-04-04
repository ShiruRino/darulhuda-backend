<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    // --- 1. MENAMPILKAN DAFTAR SANTRI ---
    public function index(Request $request)
    {
        // Memuat relasi parent (orang tua)
        $query = Student::with('parent');

        // FILTER: Pencarian Nama, NISN, atau NIK
        $query->when($request->search, function ($q, $search) {
            return $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        });

        // FILTER: Kelas & Jenis Kelamin
        $query->when($request->grade, fn($q, $grade) => $q->where('grade', $grade));
        $query->when($request->gender, fn($q, $gender) => $q->where('gender', $gender));

        // SORTING: Fleksibel berdasarkan parameter
        $sortBy = $request->get('sort_by', 'name'); // Default urut abjad
        $sortDir = $request->get('sort_dir', 'asc');
        $allowedSorts = ['name', 'nisn', 'grade', 'admission_year'];
        
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir);
        }

        $students = $query->paginate(15)->appends($request->query());

        // View akan kita buat belakangan
        return view('admin.students.index', compact('students'));
    }

    // --- 2. MENYIMPAN DATA SANTRI BARU ---
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'nisn'           => 'required|string|unique:students,nisn',
            'nik'            => 'nullable|string|size:16|unique:students,nik',
            'gender'         => 'required|in:Laki-laki,Perempuan',
            'grade'          => 'required|string|max:50',
            'admission_year' => 'required|integer',
            'dormitory'      => 'nullable|string|max:100',
            'photo'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Maksimal 2MB
        ]);

        // Proses Upload Foto
        if ($request->hasFile('photo')) {
            $validated['photo_url'] = $request->file('photo')->store('photos/students', 'public');
        }

        Student::create($validated);

        return back()->with('success', 'Data santri berhasil ditambahkan.');
    }

    // --- 3. MENAMPILKAN DETAIL SANTRI (TABS PROFIL, NILAI, TAGIHAN, ABSEN) ---
    public function show($id)
    {
        $student = Student::with([
            'parent', 
            'grades' => fn($q) => $q->orderBy('academic_year', 'desc')->orderBy('semester', 'desc'),
            'invoices' => fn($q) => $q->orderBy('created_at', 'desc'),
            'attendances' => fn($q) => $q->orderBy('date', 'desc')->limit(30)
        ])->findOrFail($id);

        return view('admin.students.show', compact('student'));
    }

    // --- 4. MEMPERBARUI DATA SANTRI ---
    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'nisn'           => 'required|string|unique:students,nisn,' . $id,
            'nik'            => 'nullable|string|size:16|unique:students,nik,' . $id,
            'gender'         => 'required|in:Laki-laki,Perempuan',
            'grade'          => 'required|string|max:50',
            'admission_year' => 'required|integer',
            'dormitory'      => 'nullable|string|max:100',
            'photo'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Proses Ganti Foto
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($student->photo_url && Storage::disk('public')->exists($student->photo_url)) {
                Storage::disk('public')->delete($student->photo_url);
            }
            // Simpan foto baru
            $validated['photo_url'] = $request->file('photo')->store('photos/students', 'public');
        }

        $student->update($validated);

        return back()->with('success', 'Data santri berhasil diperbarui.');
    }

    // --- 5. MENGHAPUS DATA SANTRI ---
    public function destroy($id)
    {
        $student = Student::findOrFail($id);

        // Hapus file foto dari storage jika ada
        if ($student->photo_url && Storage::disk('public')->exists($student->photo_url)) {
            Storage::disk('public')->delete($student->photo_url);
        }

        $student->delete();

        return redirect()->route('admin.students.index')->with('success', 'Data santri beserta rekam jejaknya berhasil dihapus.');
    }
}