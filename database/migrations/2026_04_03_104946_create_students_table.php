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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); 
            $table->string('nik', 20)->unique();
            $table->string('nisn')->unique();
            $table->string('name');
            $table->enum('gender', ['L', 'P']); // Tambahan Jenis Kelamin
            $table->string('grade');
            $table->string('admission_year');
            $table->string('dormitory')->nullable();
            $table->string('photo_url')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
