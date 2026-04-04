<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('student');

        // --- FILTER & SEARCH ---
        // Pencarian Nama Santri atau NISN
        $query->when($request->search, function ($q, $search) {
            return $q->whereHas('student', function ($studentQuery) use ($search) {
                $studentQuery->where('name', 'like', "%{$search}%")
                             ->orWhere('nisn', 'like', "%{$search}%");
            });
        });

        // Filter Status (present, sick, leave, absent)
        $query->when($request->status, function ($q, $status) {
            return $q->where('status', $status);
        });

        // Filter Rentang Tanggal
        $query->when($request->from_date, function ($q, $from) {
            return $q->whereDate('date', '>=', $from);
        });
        $query->when($request->to_date, function ($q, $to) {
            return $q->whereDate('date', '<=', $to);
        });

        // --- SORTING ---
        $sortBy = $request->get('sort_by', 'date');
        $sortDir = $request->get('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $attendances = $query->paginate(15)->appends($request->query());
        $students = Student::orderBy('name', 'asc')->get(); // Untuk dropdown tambah/edit

        return view('admin.attendances.index', compact('attendances', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'status' => 'required|in:present,sick,leave,absent',
            'notes' => 'nullable|string'
        ]);

        Attendance::create($request->all());

        return back()->with('success', 'Data absensi berhasil dicatat.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:present,sick,leave,absent',
            'notes' => 'nullable|string'
        ]);

        $attendance = Attendance::findOrFail($id);
        $attendance->update($request->only(['status', 'notes']));

        return back()->with('success', 'Data absensi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Attendance::findOrFail($id)->delete();
        return back()->with('success', 'Data absensi berhasil dihapus.');
    }
}