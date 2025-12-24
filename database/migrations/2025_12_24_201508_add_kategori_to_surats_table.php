<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->string('kategori')->after('nomor_surat')->nullable();
        });

        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->string('kategori')->after('nomor_surat')->nullable();
        });
    }

    public function down()
    {
        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};
