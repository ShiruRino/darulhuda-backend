<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_guidances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            
            // Jenis catatan: Prestasi, Pelanggaran, atau Bimbingan
            $table->enum('type', ['achievement', 'violation', 'guidance']); 
            
            $table->date('date'); // Tanggal kejadian/bimbingan
            $table->string('title'); // Contoh: "Juara 1 Lomba Adzan", "Terlambat Shalat Subuh"
            $table->text('description'); // Detail kejadian
            $table->integer('points')->default(0); // Poin plus/minus (contoh: +10 atau -5)
            $table->string('handled_by')->nullable(); // Nama Ustadz/Ustadzah yang menangani
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_guidances');
    }
};