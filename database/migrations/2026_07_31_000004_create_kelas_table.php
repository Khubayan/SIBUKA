<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurusan_id')->constrained('jurusan')->cascadeOnDelete();
            $table->string('nama', 50);
            $table->enum('tingkat', ['X', 'XI', 'XII']);
            $table->foreignId('wali_kelas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tahun_ajaran', 9)->default('2025/2026');
            $table->timestamps();

            $table->unique(['jurusan_id', 'nama', 'tahun_ajaran']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
