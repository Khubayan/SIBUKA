<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 20)->unique();
            $table->string('nisn', 20)->nullable()->unique();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->date('tanggal_lahir');
            $table->text('alamat')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('foto')->nullable();
            $table->foreignId('kelas_id')->constrained('kelas')->restrictOnDelete();
            $table->integer('total_poin')->default(0);
            $table->string('status')->default('aktif');
            $table->timestamps();
            $table->softDeletes();

            $table->index('kelas_id');
            $table->index('nis');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
