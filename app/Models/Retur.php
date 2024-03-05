<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $fillable = ['id', 'user_id', 'transaksi_id', 'tanggal', 'total', 'jenis', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function returDetail()
    {
        return $this->hasMany(ReturDetail::class)->with('pemasokBarangDetail');
    }
    public function onlyReturDetail()
    {
        return $this->hasMany(ReturDetail::class);
    }
}
