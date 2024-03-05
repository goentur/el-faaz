<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturDetail extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $fillable = ['id', 'retur_id', 'transaksi_detail_id', 'pemasok_barang_detail_id', 'tanggal', 'kuantitas', 'harga'];

    public function retur()
    {
        return $this->belongsTo(Retur::class)->with('user');
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
