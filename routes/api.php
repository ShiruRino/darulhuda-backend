    <?php

use App\Http\Controllers\Api\AcademicCalendarController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\GradeController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\StudentGuidanceController;
use App\Http\Controllers\Api\SurveyController;
use Illuminate\Support\Facades\Route;

    // --- ROUTE PUBLIC (Tidak butuh token) ---
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // --- ROUTE PRIVATE (Wajib bawa Bearer Token di Header) ---
    Route::middleware('auth:sanctum')->group(function () {
        
        Route::post('/update-fcm-token', [AuthController::class, 'updateFcmToken']);
        
        // Auth & Profile
        Route::post('/logout', [AuthController::class, 'logout']);
        
        // --- FITUR ORANG TUA (UMUM) ---
        // Melihat daftar anak yang sudah terhubung
        Route::get('/students', [StudentController::class, 'index']);
        // Menghubungkan akun dengan data anak (Input NISN)
        Route::post('/students/link', [StudentController::class, 'linkStudent']);
        // Endpoint Utama Dashboard Mobile App
        Route::get('/dashboard', [DashboardController::class, 'index']);
        
        // --- FITUR SPESIFIK ANAK (DILINDUNGI MIDDLEWARE) ---
        // Checkout WhatsApp (Logika keamanan sudah ada di dalam controller-nya sendiri)
        Route::get('/invoices/{invoice_id}/checkout', [InvoiceController::class, 'checkout']);
        
        // --- FITUR INFORMASI & LAYANAN ---
        Route::get('/announcements', [AnnouncementController::class, 'index']);
        Route::post('/feedbacks', [FeedbackController::class, 'store']);
        Route::get('/calendar', [AcademicCalendarController::class, 'index']);
        Route::get('/students/{student_id}/grades', [GradeController::class, 'index']);
        
        Route::get('/students/{student_id}/guidances', [StudentGuidanceController::class, 'index']);
        
        // Endpoint Survei
        Route::get('/surveys', [SurveyController::class, 'index']); // List survei
        Route::get('/surveys/{id}', [SurveyController::class, 'show']); // Get pertanyaan
        Route::post('/surveys/{id}/submit', [SurveyController::class, 'submit']); // Submit jawaban
        // --- PROFIL USER ---
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::put('/profile/password', [ProfileController::class, 'updatePassword']);
        // Wajib melewati pengecekan: Apakah anak ini benar milik user yang sedang login?
        Route::middleware('check.student')->group(function () {
            
            // Detail Anak
            Route::get('/students/{id}', [StudentController::class, 'show']);
            
            // Tagihan per Anak
            Route::get('/students/{student_id}/invoices', [InvoiceController::class, 'index']);
            
            // Absensi per Anak
            Route::get('/students/{student_id}/attendance', [AttendanceController::class, 'index']);
        });
            
    });