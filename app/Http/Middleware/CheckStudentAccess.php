<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStudentAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            return $next($request);
        }

        $studentId = $request->route('id') ?? $request->route('student_id');

        if ($studentId) {
            $student = \App\Models\Student::find($studentId);

            // Cek apakah santri ada dan user_id-nya sama dengan id parent yang login
            if (!$student || $student->user_id !== $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Akses ditolak. Anda tidak memiliki izin untuk melihat data santri ini.'
                ], 403);
            }
        }

        return $next($request);
    }
}