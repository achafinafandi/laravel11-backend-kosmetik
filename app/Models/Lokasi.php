<?php

// 8. Model untuk tabel tb_lokasi
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Lokasi extends Model
{
    use HasFactory;

    protected $table = 'tb_lokasi';
    protected $fillable = ['nama_lokasi', 'deskripsi_lokasi', 'created_at', 'updated_at'];

    public function raks()
    {
        return $this->hasMany(Rak::class, 'id_lokasi');
    }

}
