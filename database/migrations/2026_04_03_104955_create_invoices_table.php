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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('title'); // misal: "SPP Januari 2026"
            $table->decimal('amount', 12, 2); // Nominal tagihan
            $table->date('due_date'); // Jatuh tempo
            $table->enum('status', ['unpaid', 'pending_verification', 'paid'])->default('unpaid');
            $table->string('payment_proof_url')->nullable(); // Opsional jika admin minta bukti transfer diunggah
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
