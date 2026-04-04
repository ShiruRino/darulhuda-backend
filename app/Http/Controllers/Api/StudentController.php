<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    // Mengambil daftar anak milik orang tua yang sedang login
    public function index(Request $request)
    {
        // auth()->user() otomatis mengambil data user berdasarkan token Sanctum
        $students = $request->user()->students;

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mengambil data anak',
            'data' => StudentResource::collection($students)
        ]);
    }

    // Mengambil detail satu anak
    public function show(Request $request, $id)
    {
        // Cari data anak, pastikan itu anak dari user yang sedang login
        $student = Student::where('id', $id)
                          ->where('user_id', $request->user()->id)
                          ->first();

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data anak tidak ditemukan atau Anda tidak memiliki akses'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail anak berhasil diambil',
            'data' => new StudentResource($student)
        ]);
    }
    public function linkStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nisn' => 'required|string', // Berdasarkan NISN
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        // Cari santri berdasarkan NISN yang belum diklaim orang tua lain
        $student = Student::where('nisn', $request->nisn)->first();

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data santri dengan NISN tersebut tidak ditemukan.'
            ], 404);
        }

        if ($student->user_id !== null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Santri ini sudah terhubung dengan akun orang tua lain.'
            ], 400);
        }

        // Hubungkan santri dengan user_id orang tua yang sedang login
        $student->update([
            'user_id' => $request->user()->id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil menambahkan ' . $student->name . ' ke daftar anak Anda.',
            'data' => $student
        ]);
    }
}
