<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $fillable = ['id', 'user_id', 'pemasok_id', 'tanggal', 'total', 'keterangan', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class);
    }
    public function pembelianDetail()
    {
        return $this->hasMany(PembelianDetail::class);
    }
    public function angsuranDetail()
    {
        return $this->hasManyThrough(
            AngsuranDetail::class, // Model tujuan
            Angsuran::class, // Model perantara
            'transaksi_id', // Kunci asing di model perantara (Angsuran)
            'angsuran_id', // Kunci lokal di model tujuan (AngsuranDetail)
            'id', // Kunci primer lokal di model awal (Pembelian)
            'id' // Kunci primer di model perantara (Angsuran)
        );
    }
}
