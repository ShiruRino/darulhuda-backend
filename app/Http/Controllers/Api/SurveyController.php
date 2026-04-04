<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SurveyController extends Controller
{
    // 1. Menampilkan daftar survei yang aktif
    public function index(Request $request)
    {
        $userId = $request->user()->id; // Asumsi menggunakan Sanctum Auth

        $surveys = Survey::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('end_date')
                      ->orWhereDate('end_date', '>=', now());
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($survey) use ($userId) {
                // Cek apakah user ini sudah pernah mengisi survei ini
                $isSubmitted = SurveyResponse::where('survey_id', $survey->id)
                                             ->where('user_id', $userId)
                                             ->exists();
                return [
                    'id' => $survey->id,
                    'judul' => $survey->title,
                    'deskripsi' => $survey->description,
                    'batas_waktu' => $survey->end_date,
                    'is_submitted' => $isSubmitted // True jika sudah mengisi, False jika belum
                ];
            });

        return response()->json(['status' => 'success', 'data' => $surveys]);
    }

    // 2. Mengambil detail pertanyaan untuk 1 survei
    public function show($id)
    {
        $survey = Survey::with(['questions' => function($q) {
            $q->select('id', 'survey_id', 'question_text', 'type', 'is_required');
        }])->where('is_active', true)->findOrFail($id);

        return response()->json(['status' => 'success', 'data' => $survey]);
    }

    // 3. Menyimpan jawaban survei dari orang tua
    public function submit(Request $request, $id)
    {
        $userId = $request->user()->id;
        $survey = Survey::where('is_active', true)->findOrFail($id);

        // Validasi: Cek apakah sudah pernah mengisi
        $alreadySubmitted = SurveyResponse::where('survey_id', $id)->where('user_id', $userId)->exists();
        if ($alreadySubmitted) {
            return response()->json(['status' => 'error', 'message' => 'Anda sudah mengisi survei ini sebelumnya.'], 403);
        }

        // Cek apakah tanggal sudah kedaluwarsa
        if ($survey->end_date && \Carbon\Carbon::parse($survey->end_date)->isPast() && !\Carbon\Carbon::parse($survey->end_date)->isToday()) {
            return response()->json(['status' => 'error', 'message' => 'Masa pengisian survei ini telah berakhir.'], 403);
        }

        $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:survey_questions,id',
            'answers.*.answer_value' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Buat record Response (Responden)
            $response = SurveyResponse::create([
                'survey_id' => $survey->id,
                'user_id' => $userId
            ]);

            // Simpan detail jawaban
            foreach ($request->answers as $ans) {
                $response->answers()->create([
                    'survey_question_id' => $ans['question_id'],
                    'answer_value' => $ans['answer_value']
                ]);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Terima kasih, jawaban survei Anda berhasil disimpan!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan jawaban.'], 500);
        }
    }
}