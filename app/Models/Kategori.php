<?php

// 3. Model untuk tabel tb_kategori
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Kategori extends Model
{
    use HasFactory;

    protected $table = 'tb_kategori';
    protected $fillable = ['nama_kategori', 'created_at', 'updated_at'];

    // Relasi ke barang
    public function barangs()
    {
        return $this->hasMany(Barang::class, 'id_kategori');
    }
}
