<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PemasokBarangDetail extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class);
    }
    public function barangDetail()
    {
        return $this->belongsTo(BarangDetail::class)->with('barang', 'satuan', 'warna', 'ukuran');
    }
    public function pembelianDetail()
    {
        return $this->hasMany(PembelianDetail::class)->with('pembelian')->orderBy('tanggal', 'desc');
    }
    public function penjualanDetail()
    {
        return $this->hasMany(PenjualanDetail::class)->with('penjualan')->orderBy('tanggal', 'desc');
    }
}
