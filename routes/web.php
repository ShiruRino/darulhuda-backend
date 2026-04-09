<?php
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\Admin\GradeController;
use App\Http\Controllers\Admin\ParentUserController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\StudentGuidanceController;
use App\Http\Controllers\Admin\SurveyController;
use Illuminate\Support\Facades\Route;

// Pastikan Admin harus login dulu (Kamu bisa setup auth view standar Laravel nanti)
Route::prefix('admin')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('admin.login.post');
    });
    Route::middleware('auth')->group(function(){
        
        // Route Logout
            Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
        // Dashboard (Sudah ada)
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        
        // --- MANAJEMEN PEMBAYARAN ---
        Route::get('/payments', [PaymentController::class, 'index'])->name('admin.payments.index');
        
        // Action Master Invoice
        Route::post('/payments/master', [PaymentController::class, 'storeMaster'])->name('admin.payments.master.store');
        Route::put('/payments/master/{id}', [PaymentController::class, 'updateMaster'])->name('admin.payments.master.update');
        Route::delete('/payments/master/{id}', [PaymentController::class, 'destroyMaster'])->name('admin.payments.master.destroy');
        
        // Action Invoice Santri
        Route::put('/payments/invoice/{id}/status', [PaymentController::class, 'updateInvoiceStatus'])->name('admin.payments.invoice.update_status');
        
        Route::get('/attendances', [AttendanceController::class, 'index'])->name('admin.attendances.index');
        Route::post('/attendances', [AttendanceController::class, 'store'])->name('admin.attendances.store');
        Route::put('/attendances/{id}', [AttendanceController::class, 'update'])->name('admin.attendances.update');
        Route::delete('/attendances/{id}', [AttendanceController::class, 'destroy'])->name('admin.attendances.destroy');
    
        Route::get('/announcements', [AnnouncementController::class, 'index'])->name('admin.announcements.index');
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('admin.announcements.store');
        Route::put('/announcements/{id}', [AnnouncementController::class, 'update'])->name('admin.announcements.update');
        Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy'])->name('admin.announcements.destroy');
    
        Route::get('/parents', [ParentUserController::class, 'index'])->name('admin.parents.index');
        Route::put('/parents/{id}', [ParentUserController::class, 'update'])->name('admin.parents.update');
        Route::put('/parents/{id}/password', [ParentUserController::class, 'changePassword'])->name('admin.parents.password');
        Route::patch('/parents/{id}/status', [ParentUserController::class, 'toggleStatus'])->name('admin.parents.status');
        Route::delete('/parents/{id}', [ParentUserController::class, 'destroy'])->name('admin.parents.destroy');
    
        Route::resource('calendar', \App\Http\Controllers\Admin\AcademicCalendarController::class)->names('admin.calendar');
    
        Route::resource('grades', GradeController::class)->names('admin.grades');
        // Manajemen Kritik & Saran
        Route::get('/feedbacks', [FeedbackController::class, 'index'])->name('admin.feedbacks.index');
        Route::patch('/feedbacks/{id}/read', [FeedbackController::class, 'markAsRead'])->name('admin.feedbacks.read');
        Route::delete('/feedbacks/{id}', [FeedbackController::class, 'destroy'])->name('admin.feedbacks.destroy');
    
        Route::resource('students', StudentController::class)->except(['create', 'edit'])->names('admin.students');
    
        Route::resource('guidances', StudentGuidanceController::class)->except(['create', 'edit', 'show'])->names('admin.guidances');
    
        // Manajemen Survei
        Route::get('/surveys', [SurveyController::class, 'index'])->name('admin.surveys.index');
        Route::post('/surveys', [SurveyController::class, 'store'])->name('admin.surveys.store');
        
        // TAMBAHKAN ROUTE INI:
        Route::get('/surveys/{id}/responses', [SurveyController::class, 'showResponses'])->name('admin.surveys.responses');
        
        Route::patch('/surveys/{id}/status', [SurveyController::class, 'toggleStatus'])->name('admin.surveys.status');
        Route::delete('/surveys/{id}', [SurveyController::class, 'destroy'])->name('admin.surveys.destroy');
    
        // Dokumen Digital (PDF)
        Route::get('/documents/student/{id}/card', [DocumentController::class, 'generateStudentCard'])->name('admin.documents.card');
        Route::get('/documents/invoice/{id}/pdf', [DocumentController::class, 'generateInvoice'])->name('admin.documents.invoice');

        // Pengaturan Akun Admin
        Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile.index');
        Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('admin.profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('admin.profile.password');

        // Pengaturan Aplikasi
        Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('admin.settings.update');
    });
});