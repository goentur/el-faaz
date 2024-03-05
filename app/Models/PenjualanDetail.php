<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $fillable = ['id', 'penjualan_id', 'pemasok_barang_detail_id', 'tanggal', 'kuantitas', 'harga', 'status'];
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class)->with('user');
    }
    public function pemasokBarangDetail()
    {
        return $this->belongsTo(PemasokBarangDetail::class)->with('barangDetail', 'pemasok');
    }
    public function onlyPemasokBarangDetail()
    {
        return $this->belongsTo(PemasokBarangDetail::class, 'pemasok_barang_detail_id', 'id');
    }
}
