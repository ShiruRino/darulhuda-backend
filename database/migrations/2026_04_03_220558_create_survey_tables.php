<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Master Survei
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Contoh: "Survei Kepuasan Layanan Kantin"
            $table->text('description')->nullable();
            $table->date('end_date')->nullable(); // Batas akhir pengisian
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tabel Daftar Pertanyaan
        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->string('question_text');
            $table->enum('type', ['rating', 'text']); // rating = 1-5, text = isian paragraf
            $table->boolean('is_required')->default(true);
            $table->timestamps();
        });

        // 3. Tabel Riwayat Responden (Orang Tua yang mengisi)
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // ID Orang Tua
            $table->timestamps();
            
            // Mencegah 1 orang tua mengisi survei yang sama 2 kali
            $table->unique(['survey_id', 'user_id']);
        });

        // 4. Tabel Jawaban Detail
        Schema::create('survey_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_response_id')->constrained()->cascadeOnDelete();
            $table->foreignId('survey_question_id')->constrained()->cascadeOnDelete();
            $table->text('answer_value')->nullable(); // Berisi angka "1-5" atau teks uraian
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_answers');
        Schema::dropIfExists('survey_responses');
        Schema::dropIfExists('survey_questions');
        Schema::dropIfExists('surveys');
    }
};