<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianDetail extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $fillable = ['id', 'pembelian_id', 'pemasok_barang_detail_id', 'tanggal', 'kuantitas', 'harga'];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class)->with('user');
    }
    public function pemasokBarangDetail()
    {
        return $this->belongsTo(PemasokBarangDetail::class)->with('pemasok', 'barangDetail');
    }
}
