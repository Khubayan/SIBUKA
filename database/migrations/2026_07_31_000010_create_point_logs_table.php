<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('case_record_id')->nullable()->constrained('case_records')->nullOnDelete();
            $table->integer('poin_sebelum');
            $table->integer('poin_perubahan');
            $table->integer('poin_sesudah');
            $table->string('keterangan');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_logs');
    }
};
