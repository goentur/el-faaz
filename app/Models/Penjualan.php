<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $fillable = ['id', 'user_id', 'metode_pembayaran_id', 'anggota_id', 'nama_pembeli', 'tanggal', 'bayar',  'total', 'ongkir', 'jenis', 'status'];
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }
    public function penjualanDetail()
    {
        return $this->hasMany(PenjualanDetail::class)->with('pemasokBarangDetail');
    }
    public function retur()
    {
        return $this->hasMany(Retur::class, 'transaksi_id', 'id')->where(['jenis' => 2]);
    }
    public function returWithDetail()
    {
        return $this->hasMany(Retur::class, 'transaksi_id', 'id')->where(['jenis' => 2])->with('user', 'onlyReturDetail');
    }
}
