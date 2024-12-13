<?php


// 6. Model untuk tabel tb_keluar
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Keluar extends Model
{
    use HasFactory;

    protected $table = 'tb_keluar';
    protected $fillable = ['id_barang', 'id_user', 'id_lokasi_asal', 'jumlah_keluar', 'harga_jual_per_pcs', 'tujuan_barang', 'created_at', 'updated_at'];

    // Relasi ke barang
   // Relasi ke barang
   public function barang(): BelongsTo
   {
       return $this->belongsTo(Barang::class, 'id_barang');
   }

   // Relasi ke lokasi asal
   public function lokasiAsal(): BelongsTo
   {
       return $this->belongsTo(Rak::class, 'id_lokasi_asal');
   }

   // Relasi ke user
   public function user(): BelongsTo
   {
       return $this->belongsTo(User::class, 'id_user');
   }
}
