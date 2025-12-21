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
        Schema::create('disposisi_surat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_masuk_id')->constrained('surat_masuk')->onDelete('cascade');
            $table->foreignId('pengirim_id')->constrained('users'); // Siapa yg mendisposisikan
            $table->foreignId('penerima_id')->constrained('users'); // Siapa yg terima
            $table->text('instruksi')->nullable();

            // LOGIC WAJIB BACA
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            $table->enum('status', ['pending', 'selesai'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposisi_surat');
    }
};
