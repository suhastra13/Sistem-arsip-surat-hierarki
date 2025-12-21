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
        Schema::create('surat_keluar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembuat_id')->constrained('users'); // Staf yg buat
            $table->string('nomor_surat')->nullable(); // Awalnya kosong
            $table->date('tanggal_surat');
            $table->text('perihal');
            $table->string('file_path');

            // TRACKING STATUS
            $table->enum('status_acc', ['draft', 'pending_kasi', 'pending_kabid', 'acc', 'revisi', 'ditolak'])->default('draft');

            // Posisi surat sedang di meja siapa
            $table->unsignedBigInteger('posisi_saat_ini')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_keluar');
    }
};
