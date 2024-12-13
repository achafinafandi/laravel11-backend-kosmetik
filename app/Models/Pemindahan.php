<?php

// 7. Model untuk tabel tb_pemindahan
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Pemindahan extends Model
{
    use HasFactory;

    protected $table = 'tb_pemindahan';
    protected $fillable = ['id_barang', 'id_user', 'id_lokasi_asal', 'id_lokasi_tujuan', 'jumlah_barang', 'alasan_pemindahan', 'created_at', 'updated_at'];

    // Relasi ke barang
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    // Relasi ke user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    
    // Relasi ke lokasi asal
    public function lokasiAsal(): BelongsTo
    {
        return $this->belongsTo(Rak::class, 'id_lokasi_asal');
    }

    // Relasi ke lokasi tujuan
    public function lokasiTujuan(): BelongsTo
    {
        return $this->belongsTo(Rak::class, 'id_lokasi_tujuan');
    }

}