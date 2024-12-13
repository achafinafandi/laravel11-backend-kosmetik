<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rak extends Model
{
    use HasFactory;

    protected $table = 'tb_rak';
    protected $fillable = ['id_barang', 'id_lokasi', 'nama_rak', 'jumlah_stok','deskripsi_rak', 'created_at', 'updated_at'];

    // Relasi ke barang
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    // Relasi ke lokasi
    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }
    // Relasi ke barang masuk
    public function Masuk()
    {
        return $this->hasMany(Masuk::class, 'id_lokasi');
    }

    // Relasi ke barang keluar
    public function Keluar()
    {
        return $this->hasMany(Keluar::class, 'id_lokasi_asal');
    }
}
