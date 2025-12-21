<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            // Role & Hierarki
            $table->enum('role', ['admin', 'kabid', 'kasi', 'staff']);
            $table->unsignedBigInteger('parent_id')->nullable(); // ID Atasan
            $table->string('jabatan')->nullable(); // Nama jabatan detail
            $table->timestamps();

            // Relasi: parent_id mengarah ke id di tabel users sendiri
            $table->foreign('parent_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
