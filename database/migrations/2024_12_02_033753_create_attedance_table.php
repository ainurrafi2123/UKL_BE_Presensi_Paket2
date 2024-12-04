<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk membuat tabel.
     */
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('user_id'); // Kolom foreign key
            $table->date('date'); // Tanggal presensi
            $table->time('time'); // Waktu presensi
            $table->enum('status', ['hadir', 'izin', 'sakit','alpa']); // Status presensi
            $table->timestamps(); // Untuk created_at dan updated_at

            // Definisi foreign key ke tabel users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Rollback perubahan.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
