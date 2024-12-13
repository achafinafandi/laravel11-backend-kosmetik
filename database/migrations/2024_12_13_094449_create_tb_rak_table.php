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
        Schema::create('tb_rak', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_barang')->constrained('tb_barang')->onDelete('cascade');
            $table->string('nama_rak', 255);
            $table->foreignId('id_lokasi')->constrained('tb_lokasi')->onDelete('cascade');
            $table->integer('jumlah_stok')->default(0);
            $table->string('deskripsi_rak', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_rak');
    }
};
