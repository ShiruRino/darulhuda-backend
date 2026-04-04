<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SurveyController extends Controller
{
    public function index()
    {
        // Ambil data survei beserta jumlah responden yang sudah mengisi
        $surveys = Survey::withCount('responses')
                         ->orderBy('created_at', 'desc')
                         ->paginate(10);

        return view('admin.surveys.index', compact('surveys'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'end_date' => 'nullable|date|after_or_equal:today',
            // Validasi Array Pertanyaan
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.type' => 'required|in:rating,text',
            'questions.*.is_required' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            // 1. Simpan Master Survei
            $survey = Survey::create([
                'title' => $request->title,
                'description' => $request->description,
                'end_date' => $request->end_date,
                'is_active' => true,
            ]);

            // 2. Simpan Daftar Pertanyaan
            foreach ($request->questions as $q) {
                $survey->questions()->create([
                    'question_text' => $q['question_text'],
                    'type' => $q['type'],
                    'is_required' => $q['is_required'],
                ]);
            }

            DB::commit();
            return back()->with('success', 'Survei dan pertanyaan berhasil dipublikasikan!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan survei: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        $survey = Survey::findOrFail($id);
        $survey->is_active = !$survey->is_active;
        $survey->save();

        return back()->with('success', 'Status survei berhasil diubah.');
    }

    public function destroy($id)
    {
        // Karena di migrasi kita pakai cascadeOnDelete, menghapus survei 
        // akan otomatis menghapus pertanyaan dan jawaban di database.
        Survey::findOrFail($id)->delete();
        return back()->with('success', 'Survei berhasil dihapus secara permanen.');
    }
}