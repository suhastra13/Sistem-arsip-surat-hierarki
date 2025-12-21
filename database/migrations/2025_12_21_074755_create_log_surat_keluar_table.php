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
        Schema::create('log_surat_keluar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_keluar_id')->constrained('surat_keluar')->onDelete('cascade');
            $table->foreignId('from_user_id')->constrained('users'); // Pengirim (Kasi/Kabid)
            $table->foreignId('to_user_id')->constrained('users');   // Tujuan (Staf/Kasi)

            $table->text('catatan_revisi')->nullable();
            $table->string('aksi');

            // LOGIC WAJIB BACA FEEDBACK
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_surat_keluar');
    }
};
