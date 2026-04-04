<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicCalendar;
use Illuminate\Http\Request;

class AcademicCalendarController extends Controller
{
    public function index(Request $request)
    {
        $query = AcademicCalendar::query();

        // Search & Filter
        $query->when($request->search, function ($q, $search) {
            return $q->where('title', 'like', "%{$search}%");
        });

        $query->when($request->type, function ($q, $type) {
            return $q->where('type', $type);
        });

        // Urutkan berdasarkan tanggal terdekat
        $events = $query->orderBy('start_date', 'asc')->paginate(15);

        return view('admin.calendar.index', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'type' => 'required|in:holiday,exam,event,other',
        ]);

        AcademicCalendar::create($request->all());
        return back()->with('success', 'Agenda akademik berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'start_date' => 'required|date',
            'type' => 'required',
        ]);

        AcademicCalendar::findOrFail($id)->update($request->all());
        return back()->with('success', 'Agenda berhasil diperbarui.');
    }

    public function destroy($id)
    {
        AcademicCalendar::findOrFail($id)->delete();
        return back()->with('success', 'Agenda berhasil dihapus.');
    }
}