<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('subject'); // Menggunakan string sesuai instruksi
            $table->string('academic_year'); // Contoh: 2025/2026
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->enum('type', ['Tugas', 'UTS', 'UAS', 'Rapor']);
            $table->decimal('score', 5, 2); // Nilai maksimal 100.00
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
