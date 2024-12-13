<?php

// 4. Model untuk tabel tb_supplier
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Supplier extends Model
{
    use HasFactory;

    protected $table = 'tb_supplier';
    protected $fillable = ['nama', 'kontak', 'created_at', 'updated_at'];

    // Relasi ke barang
    public function barangs()
    {
        return $this->hasMany(Barang::class, 'id_supplier');
    }
    public function masuks()
    {
        return $this->hasMany(Masuk::class, 'id_supplier');
    }
}


