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
        Schema::create('master_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Contoh: "SPP Bulanan"
            $table->decimal('amount', 12, 2);
            $table->boolean('is_active')->default(true); // Untuk kontrol apakah otomatis ditambahkan atau tidak
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_invoices');
    }
};
