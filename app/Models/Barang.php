<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barang extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class);
    }
    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
    }
    public function ukuran()
    {
        return $this->belongsToMany(Ukuran::class, BarangUkuran::class);
    }
}
