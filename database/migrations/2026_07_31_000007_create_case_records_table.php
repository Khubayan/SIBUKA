<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('case_type_id')->constrained('case_types')->restrictOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->string('kategori');
            $table->string('judul');
            $table->text('deskripsi');
            $table->integer('poin');
            $table->date('tanggal_kejadian');
            $table->string('bukti_path')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'tanggal_kejadian']);
            $table->index('kategori');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_records');
    }
};
