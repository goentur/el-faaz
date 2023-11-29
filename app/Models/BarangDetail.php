<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarangDetail extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class);
    }
    public function barang()
    {
        return $this->belongsTo(Barang::class)->with('warna');
    }
    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
    }
    public function ukuran()
    {
        return $this->belongsToMany(Ukuran::class, UkuranBarangDetail::class);
    }
}
